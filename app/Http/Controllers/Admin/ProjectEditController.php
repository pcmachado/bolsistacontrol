<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Position;
use App\Models\ScholarshipHolder;
use App\Models\Course;
use App\Models\FundingSource;
use App\Http\Requests\Project\UpdateProjectFundingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Project\UpdateProjectScholarsRequest;
use App\Http\Requests\Project\UpdateProjectCoursesRequest;

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

    public function updateScholars(
        UpdateProjectScholarsRequest $request,
        Project $project
    ) {
        DB::transaction(function () use ($project, $request) {

            $sync = collect($request->validated()['scholarships'])
                ->mapWithKeys(fn ($s) => [
                    $s['scholarship_holder_id'] => [
                        'position_id'     => $s['position_id'],
                        'weekly_workload' => $s['weekly_workload'],
                        'status'          => $s['status'],
                        'start_date'      => $s['start_date'] ?? $project->start_date,
                        'end_date'        => $s['end_date'] ?? null,
                    ]
                ])
                ->toArray();

            // Atualiza / cria
            $project->scholarshipHolders()->syncWithoutDetaching($sync);

            // Remove desmarcados
            $selectedIds = array_keys($sync);

            $project->scholarshipHolders()
                ->whereNotIn('scholarship_holder_id', $selectedIds)
                ->detach();
        });

        return redirect()
            ->route('admin.projects.edit.scholars', $project)
            ->with('success', 'Bolsistas atualizados com sucesso.');
    }

    public function updateCourses(
    UpdateProjectCoursesRequest $request,
    Project $project
    ) {
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
                        'amount'     => $f['amount'],
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
                ->updateExistingPivot('status', 'inactive');
        });

        return redirect()
            ->route('admin.projects.edit.funding', $project)
            ->with('success', 'Fontes de fomento atualizadas com sucesso.');
    }
}
