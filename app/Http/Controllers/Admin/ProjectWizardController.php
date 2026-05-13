<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\DocumentTemplate;
use App\Models\FundingSource;
use App\Models\Institution;
use App\Models\Position;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectWizardController extends Controller
{
    private function ensureStep(Project $project, string $expected)
    {
        if (empty($project->wizard_step)) {
            $project->update(['wizard_step' => 'step1']);
        }

        if ($project->wizard_step !== $expected) {
            abort(403, 'Etapa invÃ¡lida do wizard.');
        }
    }

    private function advance(Project $project, string $next)
    {
        $project->update(['wizard_step' => $next]);
    }

    public function createStep1()
    {
        $user = Auth::user();

        return view('admin.projects.wizard.step1', [
            'institutions' => Institution::query()
                ->whereIn('id', $user->accessibleInstitutionIds())
                ->orderBy('name')
                ->get(),
            'units' => Unit::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $user->visibleUnitIds())
                ->orderBy('name')
                ->get(),
            'templates' => DocumentTemplate::query()
                ->where('active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function storeStep1(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_id' => 'required|exists:units,id',
            'institution_id' => 'required|exists:institutions,id',
            'document_template_id' => 'nullable|exists:document_templates,id',
            'monthly_report_template_id' => 'nullable|exists:document_templates,id',
            'final_report_template_id' => 'nullable|exists:document_templates,id',
            'report_title' => 'nullable|string|max:255',
            'report_subtitle' => 'nullable|string|max:255',
            'report_header_html' => 'nullable|string',
            'report_footer_html' => 'nullable|string',
            'report_logo' => 'nullable|image|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if (! Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            $data['institution_id'] = Auth::user()->resolvedInstitutionId();
        }

        $project = Project::create($this->preparePayload($request, $data) + [
            'wizard_step' => 'step2',
            'status' => Project::STATUS_DRAFT,
        ]);

        return redirect()->route('admin.projects.create.step2', $project);
    }

    public function editStep1(Project $project)
    {
        $user = Auth::user();

        return view('admin.projects.wizard.step1', [
            'project' => $project,
            'institutions' => Institution::query()
                ->whereIn('id', $user->accessibleInstitutionIds())
                ->orderBy('name')
                ->get(),
            'units' => Unit::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $user->visibleUnitIds())
                ->orderBy('name')
                ->get(),
            'templates' => DocumentTemplate::query()
                ->where('active', true)
                ->orderBy('name')
                ->get(),
            'editing' => true,
        ]);
    }

    public function updateStep1(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_id' => 'required|exists:units,id',
            'institution_id' => 'required|exists:institutions,id',
            'document_template_id' => 'nullable|exists:document_templates,id',
            'monthly_report_template_id' => 'nullable|exists:document_templates,id',
            'final_report_template_id' => 'nullable|exists:document_templates,id',
            'report_title' => 'nullable|string|max:255',
            'report_subtitle' => 'nullable|string|max:255',
            'report_header_html' => 'nullable|string',
            'report_footer_html' => 'nullable|string',
            'report_logo' => 'nullable|image|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if (! Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            $data['institution_id'] = Auth::user()->resolvedInstitutionId();
        }

        $project->update($this->preparePayload($request, $data, $project));
        $this->advance($project, 'step2');

        return redirect()->route('admin.projects.create.step2', $project);
    }

    public function createStep2(Project $project)
    {
        $this->ensureStep($project, 'step2');

        $project->load('positions');

        return view('admin.projects.wizard.step2', [
            'project' => $project,
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
                ->mapWithKeys(fn ($position) => [
                    $position['id'] => ['hourly_rate' => $position['hourly_rate']],
                ])
                ->toArray();

            $project->positions()->syncWithoutDetaching($sync);
            $this->advance($project, 'step3');
        });

        return redirect()->route('admin.projects.create.step3', $project);
    }

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
            ->filter(fn ($course) => ! empty($course['selected']) && ! empty($course['course_id']))
            ->map(fn ($course) => [
                'course_id' => $course['course_id'],
                'active' => filter_var($course['active'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'semester' => $course['semester'] ?? null,
                'year' => $course['year'] ?? null,
            ])
            ->values()
            ->all();

        $validated = validator(
            ['courses' => $filtered],
            [
                'courses' => 'required|array|min:1',
                'courses.*.course_id' => 'required|exists:courses,id',
                'courses.*.active' => 'boolean',
                'courses.*.semester' => 'nullable|string|max:50',
                'courses.*.year' => 'nullable|integer|min:2000|max:2100',
            ],
            [
                'courses.required' => 'Selecione ao menos um curso para avançar.',
                'courses.min' => 'Selecione ao menos um curso para avançar.',
            ]
        )->validate();

        DB::transaction(function () use ($project, $validated) {
            $sync = collect($validated['courses'])
                ->mapWithKeys(fn ($course) => [
                    $course['course_id'] => [
                        'active' => (bool) $course['active'],
                        'semester' => $course['semester'] ?? null,
                        'year' => $course['year'] ?? null,
                        'start_date' => $project->start_date,
                        'end_date' => $project->end_date,
                    ]
                ])
                ->toArray();

            $project->courses()->sync($sync);
            $this->advance($project, 'step4');
        });

        return redirect()->route('admin.projects.create.step4', $project);
    }

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
            ->filter(fn ($scholarship) => ! empty($scholarship['position_id']) &&
                ! empty($scholarship['weekly_workload'])
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
                ->mapWithKeys(fn ($scholarship) => [
                    $scholarship['scholarship_holder_id'] => [
                        'position_id' => $scholarship['position_id'],
                        'weekly_workload' => $scholarship['weekly_workload'] ?? 20,
                        'status' => $scholarship['status'] ?? 'active',
                        'start_date' => $scholarship['start_date'] ?? $project->start_date,
                        'end_date' => $scholarship['end_date'] ?? $project->end_date,
                    ],
                ])
                ->toArray();

            if (empty($sync)) {
                return back()->withErrors([
                    'scholarships' => 'Selecione ao menos um bolsista.',
                ]);
            }

            $project->scholarshipHolders()->syncWithoutDetaching($sync);
            $this->advance($project, 'step5');
        });

        return redirect()->route('admin.projects.create.step5', $project);
    }

    public function createStep5(Project $project)
    {
        $this->ensureStep($project, 'step5');

        $project->load('fundingSources');

        return view('admin.projects.wizard.step5', [
            'project' => $project,
            'fundingSources' => FundingSource::query()->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function storeStep5(Request $request, Project $project)
    {
        $this->ensureStep($project, 'step5');

        $filtered = collect($request->input('fundings', []))
            ->filter(fn ($funding) => ! empty($funding['selected']) && ! empty($funding['funding_source_id']))
            ->map(fn ($funding) => [
                'funding_source_id' => $funding['funding_source_id'],
                'allocated_amount' => $funding['allocated_amount'] ?? null,
            ])
            ->values()
            ->all();

        $validated = validator(
            ['fundings' => $filtered],
            [
                'fundings' => 'required|array|min:1',
                'fundings.*.funding_source_id' => 'required|exists:funding_sources,id',
                'fundings.*.allocated_amount' => 'required|numeric|min:0',
            ],
            [
                'fundings.required' => 'Selecione ao menos uma forma de fomento para avançar.',
                'fundings.min' => 'Selecione ao menos uma forma de fomento para avançar.',
            ]
        )->validate();

        DB::transaction(function () use ($project, $validated) {
            $sync = collect($validated['fundings'])
                ->mapWithKeys(fn ($funding) => [
                    $funding['funding_source_id'] => [
                        'allocated_amount' => $funding['allocated_amount'],
                        'start_date' => $funding['start_date'] ?? $project->start_date,
                        'end_date' => $funding['end_date'] ?? $project->end_date,
                        'status' => $funding['status'] ?? 'active',
                    ],
                ])
                ->toArray();

            $project->fundingSources()->sync($sync);

            // 🔥 Avança o wizard
            $this->advance($project, 'review');
        });

        return redirect()->route('admin.projects.review', $project);
    }

    public function review(Project $project)
    {
        $this->ensureStep($project, 'review');

        $project->load(['positions', 'courses', 'scholarshipHolders', 'fundingSources']);

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

    private function preparePayload(Request $request, array $validated, ?Project $project = null): array
    {
        unset($validated['report_logo']);

        if (empty($validated['document_template_id'])) {
            $validated['document_template_id'] = $validated['monthly_report_template_id']
                ?? $validated['final_report_template_id']
                ?? null;
        }

        if ($request->hasFile('report_logo')) {
            if ($project?->report_logo_path) {
                Storage::disk('public')->delete($project->report_logo_path);
            }

            $validated['report_logo_path'] = $request->file('report_logo')
                ->store('project-report-logos', 'public');
        }

        return $validated;
    }
}
