<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    Project,
    Institution,
    Unit,
    Position,
    ScholarshipHolder,
    Course,
    FundingSource
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectWizardController extends Controller
{
    /* =========================
     * Helpers
     * ========================= */
    private function ensureStep(Project $project, string $expected)
    {
        if (empty($project->wizard_step)) {
            $project->update(['wizard_step' => 'step1']);
        }

        if ($project->wizard_step !== $expected) {
            abort(403, 'Etapa inválida do wizard.');
        }
    }

    private function advance(Project $project, string $next)
    {
        $project->update(['wizard_step' => $next]);
    }

    /* =========================
     * STEP 1 – Projeto
     * ========================= */
    public function createStep1()
    {
        return view('admin.projects.wizard.step1', [
            'institutions' => Institution::all(),
            'units'        => Unit::all(),
        ]);
    }

    public function storeStep1(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'unit_id'        => 'required|exists:units,id',
            'institution_id'=> 'required|exists:institutions,id',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
        ]);

        $project = Project::update($data + [
            'wizard_step' => 'step2',
            'status'      => Project::STATUS_DRAFT,
        ]);

        return redirect()->route('admin.projects.create.step2', $project);
    }

    public function editStep1(Project $project)
    {
        // projeto existente → carregar dados
        return view('admin.projects.wizard.step1', [
            'project' => $project,
            'institutions' => Institution::all(),
            'units' => Unit::all(),
            'editing' => true,
        ]);
    }

    public function updateStep1(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'institution_id' => 'required|exists:institutions,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $project->update($data);

        // garante avanço correto
        $this->advance($project, 'step2');

        return redirect()->route('admin.projects.create.step2', $project);
    }

    /* =========================
     * STEP 2 – Cargos
     * ========================= */
    public function createStep2(Project $project)
    {
        $this->ensureStep($project, 'step2');

        $project->load('positions');

        return view('admin.projects.wizard.step2', [
            'project'   => $project,
            'positions' => Position::all(),
        ]);
    }

    public function storeStep2(Request $request, Project $project)
    {
        $this->ensureStep($project, 'step2');

        $validated = $request->validate([
            'positions' => 'required|array|min:1',
            'positions.*.id' => 'required|exists:positions,id',
            'positions.*.hourly_rate' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($project, $validated) {
            $sync = collect($validated['positions'])
                ->mapWithKeys(fn ($p) => [
                    $p['id'] => ['hourly_rate' => $p['hourly_rate']]
                ])
                ->toArray();

            $project->positions()->syncWithoutDetaching($sync);
            $this->advance($project, 'step3');
        });

        return redirect()->route('admin.projects.create.step3', $project);
    }

    /* =========================
     * STEP 3 – Cursos
     * ========================= */
    public function createStep3(Project $project)
    {
        $this->ensureStep($project, 'step3');

        $project->load('courses');

        return view('admin.projects.wizard.step3', [
            'project' => $project,
            'courses' => Course::all(),
        ]);
    }

    public function storeStep3(Request $request, Project $project)
    {
        $this->ensureStep($project, 'step3');

        $filtered = collect($request->input('courses', []))
            ->filter(fn ($c) =>
                !empty($c['course_id']) &&
                (
                    isset($c['active']) ||
                    !empty($c['semester']) ||
                    !empty($c['year'])
                )
            )
            ->values()
            ->all();

        $validated = validator(
            ['courses' => $filtered],
            [
                'courses' => 'required|array|min:1',
                'courses.*.course_id' => 'required|exists:courses,id',
                'courses.*.active' => 'nullable|boolean',
                'courses.*.semester' => 'nullable|string',
                'courses.*.year' => 'nullable|integer',
            ]
        )->validate();

        DB::transaction(function () use ($project, $validated) {
            $sync = collect($validated['courses'])
                ->mapWithKeys(fn ($c) => [
                    $c['course_id'] => [
                        'active' => isset($c['active']) ? (bool)$c['active'] : true,
                        'semester' => $c['semester'] ?? null,
                        'year' => $c['year'] ?? null,
                        'start_date' => $c['start_date'] ?? $project->start_date,
                        'end_date' => $c['end_date'] ?? $project->end_date,
                    ]
                ])
                ->toArray();

            $project->courses()->syncWithoutDetaching($sync);
            $this->advance($project, 'step4');
        });

        return redirect()->route('admin.projects.create.step4', $project);
    }

    /* =========================
     * STEP 4 – Bolsistas
     * ========================= */
    public function createStep4(Project $project)
    {
        $this->ensureStep($project, 'step4');

        $project->load([
            'positions',
            'scholarshipHolders.user',
        ]);

        return view('admin.projects.wizard.step4', [
            'project' => $project,
            'positions' => $project->positions,
            'scholarshipHolders' => ScholarshipHolder::all(),
        ]);
    }

    public function storeStep4(Request $request, Project $project)
    {
        $this->ensureStep($project, 'step4');

        $filtered = collect($request->input('scholarships', []))
            ->filter(fn ($s) => 
                !empty($s['position_id']) &&
                !empty($s['weekly_workload'])
            )
            ->values()
            ->all();

        $validated = validator(
            ['scholarships' => $filtered],
            [
                'scholarships' => 'required|array|min:1',
                'scholarships.*.scholarship_holder_id' => 'required|exists:scholarship_holders,id',
                'scholarships.*.position_id' => 'required|exists:positions,id',
                'scholarships.*.weekly_workload' => 'required|numeric|min:1',
            ]
        )->validate();

        DB::transaction(function () use ($project, $validated) {
            $sync = collect($validated['scholarships'])
                ->mapWithKeys(fn ($s) => [
                    $s['scholarship_holder_id'] => [
                        'position_id' => $s['position_id'],
                        'weekly_workload' => $s['weekly_workload'] ?? 20,
                        'status' => $s['status'] ?? 'active',
                        'start_date'        => $s['start_date'] ?? $project->start_date,
                        'end_date'          => $s['end_date'] ?? $project->end_date,
                    ]
                ])
                ->toArray();

            if (empty($sync)) {
                return back()->withErrors([
                    'scholarships' => 'Selecione ao menos um bolsista.'
                ]);
            }

            $project->scholarshipHolders()->syncWithoutDetaching($sync);

            $this->advance($project, 'step5');
        });

        return redirect()->route('admin.projects.create.step5', $project);
    }

    /* =========================
     * STEP 5 – Fomento
     * ========================= */
    public function createStep5(Project $project)
    {
        $this->ensureStep($project, 'step5');

        $project->load('fundingSources');

        return view('admin.projects.wizard.step5', [
            'project' => $project,
            'fundingSources' => FundingSource::all(),
        ]);
    }

    public function storeStep5(Request $request, Project $project)
    {
        $this->ensureStep($project, 'step5');

        // 🔥 Filtra apenas fundings válidos
        $filtered = collect($request->input('fundings', []))
            ->filter(fn ($f) =>
                !empty($f['funding_source_id']) &&
                isset($f['allocated_amount']) &&
                $f['allocated_amount'] !== ''
            )
            ->values()
            ->all();

        $validated = validator(
            ['fundings' => $filtered],
            [
                'fundings' => 'required|array|min:1',
                'fundings.*.funding_source_id' => 'required|exists:funding_sources,id',
                'fundings.*.allocated_amount' => 'required|numeric|min:0',
            ]
        )->validate();

        DB::transaction(function () use ($project, $validated) {

            $sync = collect($validated['fundings'])
                ->mapWithKeys(fn ($f) => [
                    $f['funding_source_id'] => [
                        'allocated_amount'     => $f['allocated_amount'],
                        'start_date' => $f['start_date'] ?? $project->start_date,
                        'end_date'   => $f['end_date'] ?? $project->end_date,
                        'status'     => $f['status'] ?? 'active',
                    ],
                ])
                ->toArray();

            $project->fundingSources()->syncWithoutDetaching($sync);

            // 🔥 Avança o wizard
            $this->advance($project, 'review');
        });

        return redirect()->route('admin.projects.review', $project);
    }

    /* =========================
     * REVIEW & FINALIZE
     * ========================= */
    public function review(Project $project)
    {
        $this->ensureStep($project, 'review');

        $project->load(['positions','courses','scholarshipHolders','fundingSources']);

        return view('admin.projects.wizard.review', compact('project'));
    }

    public function finalize(Project $project)
    {
        $this->ensureStep($project, 'review');

        $project->update([
            'wizard_step' => 'completed',
            'status' => 'completed',
        ]);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Projeto finalizado com sucesso!');
    }
}
