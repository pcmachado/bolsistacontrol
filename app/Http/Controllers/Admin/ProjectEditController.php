<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProjectCoursesRequest;
use App\Http\Requests\UpdateProjectFundingRequest;
use App\Http\Requests\UpdateProjectScholarsRequest;
use App\Models\Course;
use App\Models\DocumentTemplate;
use App\Models\FundingSource;
use App\Models\Institution;
use App\Models\Position;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectEditController extends Controller
{
    public function index(Project $project)
    {
        return view('admin.projects.edit.index', compact('project'));
    }

    public function general(Project $project)
    {
        $user = Auth::user();
        $institutions = Institution::query()
            ->whereIn('id', $user->accessibleInstitutionIds())
            ->orderBy('name')
            ->get();
        $templates = DocumentTemplate::query()
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view('admin.projects.edit.general', compact('project', 'institutions', 'templates'));
    }

    public function positions(Project $project)
    {
        $project->load('positions');
        $allPositions = Position::orderBy('name')->get();

        return view('admin.projects.edit.positions', compact('project', 'allPositions'));
    }

    public function updatePositions(Request $request, Project $project)
    {
        $validated = $request->validate([
            'positions' => 'required|array|min:1',
            'positions.*.id' => 'required|exists:positions,id',
            'positions.*.hourly_rate' => 'required|numeric|min:0',
        ]);

        $sync = collect($validated['positions'])
            ->filter(fn ($position) => isset($position['hourly_rate']) && $position['hourly_rate'] !== '')
            ->mapWithKeys(fn ($position) => [
                $position['id'] => ['hourly_rate' => $position['hourly_rate']],
            ])
            ->toArray();

        $project->positions()->sync($sync);

        return redirect()
            ->route('admin.projects.edit.positions', $project)
            ->with('success', 'Cargos atualizados com sucesso.');
    }

    public function courses(Project $project)
    {
        $project->load('courses');
        $courses = Course::all();

        return view('admin.projects.edit.courses', compact('project', 'courses'));
    }

    public function scholars(Project $project)
    {
        $project->load(['scholarshipHolders.user', 'positions']);
        $scholarshipHolders = ScholarshipHolder::with('user')->get();
        $currentPositionIds = $project->scholarshipHolders
            ->pluck('pivot.position_id')
            ->filter()
            ->unique()
            ->values();
        $positions = $project->positions
            ->merge(Position::query()->whereIn('id', $currentPositionIds)->get())
            ->unique('id')
            ->sortBy('name')
            ->values();

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
                        'position_id' => $s['position_id'],
                        'weekly_workload' => $s['weekly_workload'],
                        'status' => $s['status'],
                        'start_date' => $s['start_date'] ?? $project->start_date,
                        'end_date' => $s['end_date'] ?? null,
                        'edital_portaria' => $s['edital_portaria'] ?? null,
                    ],
                ])
                ->toArray();

            $project->scholarshipHolders()->syncWithoutDetaching($sync);

            $selectedIds = array_keys($sync);

            $toDisable = $project->scholarshipHolders()
                ->whereNotIn('scholarship_holder_id', $selectedIds)
                ->pluck('scholarship_holder_id');

            foreach ($toDisable as $id) {
                $project->scholarshipHolders()->updateExistingPivot($id, [
                    'status' => 'inactive',
                ]);
            }
        });

        return redirect()
            ->route('admin.projects.edit.scholars', $project)
            ->with('success', 'Bolsistas atualizados com sucesso.');
    }

    public function updateCourses(UpdateProjectCoursesRequest $request, Project $project)
    {
        DB::transaction(function () use ($project, $request) {
            $sync = collect($request->validated()['courses'])
                ->mapWithKeys(fn ($c) => [
                    $c['course_id'] => [
                        'active' => (bool) $c['active'],
                        'semester' => $c['semester'] ?? null,
                        'year' => $c['year'] ?? null,
                        'start_date' => $project->start_date,
                        'end_date' => $project->end_date,
                    ],
                ])
                ->toArray();

            $project->courses()->syncWithoutDetaching($sync);

            $activeIds = array_keys($sync);

            if (! empty($activeIds)) {
                $project->courses()
                    ->whereNotIn('course_id', $activeIds)
                    ->updateExistingPivot('active', false);
            }
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
                        'allocated_amount' => $f['allocated_amount'],
                        'start_date' => $f['start_date'] ?? $project->start_date,
                        'end_date' => $f['end_date'] ?? $project->end_date,
                        'status' => $f['status'] ?? 'active',
                    ],
                ])
                ->toArray();

            $project->fundingSources()->syncWithoutDetaching($sync);

            $activeIds = array_keys($sync);

            if (! empty($activeIds)) {
                $project->fundingSources()
                    ->whereNotIn('funding_source_id', $activeIds)
                    ->updateExistingPivot('status', 'inactive');
            }
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

        if ($project->scholarshipHolders()->where('scholarship_holder_id', $data['holder_id'])->exists()) {
            return back()->with('error', 'Bolsista jÃ¡ vinculado ao projeto.');
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
            'status' => 'inactive',
        ]);

        return back()->with('success', 'Bolsista desativado no projeto.');
    }
}
