<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Position;
use App\Models\ScholarshipHolder;
use App\Models\Course;
use App\Models\FundingSource;
use App\Http\Requests\UpdateProjectFundingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UpdateProjectScholarsRequest;
use App\Http\Requests\UpdateProjectCoursesRequest;

class ProjectEditController extends Controller
{
    public function index(Project $project)
    {
        return view('admin.projects.edit.index', compact('project'));
    }

    public function general(Project $project)
    {
        return view('admin.projects.edit.general', compact('project'));
    }

    public function courses(Project $project)
    {
        $project->load('courses');
        $courses = Course::all();
        return view('admin.projects.edit.courses', compact('project', 'courses'));
    }

    public function scholars(Project $project)
    {
        $project->load('scholarshipHolders');
        $scholarshipHolders = ScholarshipHolder::with('user')->get();
        $positions = Position::all();

        return view('admin.projects.edit.scholars', compact(
            'project',
            'scholarshipHolders',
            'positions'
        ));
    }

    public function editScholar($projectId, $holderId)
    {
        $project = Project::findOrFail($projectId);

        $holder = $project->scholarshipHolders()
            ->wherePivot('id', $holderId)
            ->firstOrFail();

        return view('projects.edit.scholar-edit', compact('project', 'holder'));
    }

    public function updateScholar(UpdateProjectScholarsRequest $request, Project $project)
    {
        DB::transaction(function () use ($project, $request) {

            $sync = collect($request->validated()['scholarships'])
                ->mapWithKeys(fn ($s) => [
                    $s['scholarship_holder_id'] => [
                        'position_id'     => $s['position_id'],
                        'weekly_workload' => $s['weekly_workload'],
                        'status'          => $s['status'],
                        'start_date'      => $s['start_date'] ?? $project->start_date,
                        'end_date'        => $s['end_date'] ?? null,
                        'edital_portaria' => $s['edital_portaria'] ?? null,
                    ]
                ])
                ->toArray();

            $project->scholarshipHolders()->syncWithoutDetaching($sync);

            $selectedIds = array_keys($sync);

            $toDisable = $project->scholarshipHolders()
                ->whereNotIn('scholarship_holder_id', $selectedIds)
                ->pluck('scholarship_holder_id');

            foreach ($toDisable as $id) {
                $project->scholarshipHolders()->updateExistingPivot($id, [
                    'status' => 'inactive'
                ]);
            }
        });

        return redirect()
            ->route('admin.projects.edit.scholars', $project)
            ->with('success', 'Bolsistas atualizados com sucesso.');
    
        DB::transaction(function () use ($project, $request) {

            $sync = collect($request->validated()['courses'])
                ->mapWithKeys(fn ($c) => [
                    $c['course_id'] => [
                        'active'     => (bool) $c['active'],
                        'semester'   => $c['semester'] ?? null,
                        'year'       => $c['year'] ?? null,
                        'start_date' => $project->start_date,
                        'end_date'   => $project->end_date,
                    ]
                ])
                ->toArray();

            $project->courses()->syncWithoutDetaching($sync);

            $activeIds = array_keys($sync);

            $project->courses()
                ->whereNotIn('course_id', $activeIds)
                ->updateExistingPivot('active', false);
        });

        return redirect()
            ->route('admin.projects.edit.courses', $project)
            ->with('success', 'Cursos atualizados com sucesso.');
    }

    public function funding(Project $project)
    {
        $project->load('fundingSources');
        $fundingSources = FundingSource::all();

        return view(
            'admin.projects.edit.funding',
            compact('project', 'fundingSources')
        );
    }

    public function updateFunding(
        UpdateProjectFundingRequest $request,
        Project $project
    ) {
        DB::transaction(function () use ($project, $request) {

            $sync = collect($request->validated()['fundings'])
                ->mapWithKeys(fn ($f) => [
                    $f['funding_source_id'] => [
                        'allocated_amount'     => $f['allocated_amount'],
                        'start_date' => $f['start_date'] ?? $project->start_date,
                        'end_date'   => $f['end_date'] ?? $project->end_date,
                        'status'     => $f['status'] ?? 'active',
                    ]
                ])
                ->toArray();

            // Atualiza / cria
            $project->fundingSources()->syncWithoutDetaching($sync);

            // Desativa fontes removidas
            $activeIds = array_keys($sync);

            $project->fundingSources()
                ->whereNotIn('funding_source_id', $activeIds)
                ->updateExistingPivot('status', 'finished');
        });

        return redirect()
            ->route('admin.projects.edit.funding', $project)
            ->with('success', 'Fontes de fomento atualizadas com sucesso.');
    }

    public function storeScholar(Request $request, Project $project)
    {
        $data = $request->validate([
            'holder_id' => 'required|exists:scholarship_holders,id',
            'position_id' => 'required|exists:positions,id',
            'weekly_workload' => 'required|integer|min:1|max:40',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'edital_portaria' => 'nullable|string|max:255',
        ]);

        // 🔥 evita duplicidade
        if ($project->scholarshipHolders()->where('scholarship_holder_id', $data['holder_id'])->exists()) {
            return back()->with('error', 'Bolsista já vinculado ao projeto.');
        }

        $project->scholarshipHolders()->attach($data['holder_id'], [
            'position_id' => $data['position_id'],
            'weekly_workload' => $data['weekly_workload'],
            'status' => 'active',
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'edital_portaria' => $data['edital_portaria'],
        ]);

        return back()->with('success', 'Bolsista adicionado com sucesso.');
    }

    public function destroyScholar(Project $project, $holderId)
    {
        $project->scholarshipHolders()->updateExistingPivot($holderId, [
            'status' => 'inactive'
        ]);

        return back()->with('success', 'Bolsista desativado no projeto.');
    }
}
