<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ProjectsDataTable;
use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use App\Models\Institution;
use App\Models\Project;
use App\Models\Unit;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index(ProjectsDataTable $projectsDataTable)
    {
        return $projectsDataTable->render('admin.projects.index');
    }

    public function create()
    {
        $user = Auth::user();

        $units = Unit::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $user->visibleUnitIds())
            ->orderBy('name')
            ->get();

        $institutions = Institution::query()
            ->whereIn('id', $user->accessibleInstitutionIds())
            ->orderBy('name')
            ->get();

        $templates = DocumentTemplate::query()
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view('admin.projects.create', compact('units', 'institutions', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        if (! Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            $validated['institution_id'] = Auth::user()->resolvedInstitutionId();
        }

        $validated = $this->preparePayload($request, $validated);

        Project::create($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Projeto criado com sucesso.');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorize('view', $project);

        return redirect()->route('admin.projects.edit.general', $project);
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $validated = $request->validate($this->rules());

        if (! Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            $validated['institution_id'] = Auth::user()->resolvedInstitutionId();
        }

        $validated = $this->preparePayload($request, $validated, $project);

        $project->update($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Projeto atualizado com sucesso.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('view', $project);

        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Projeto excluÃ­do com sucesso.');
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_id' => 'nullable|exists:units,id',
            'institution_id' => 'required|exists:institutions,id',
            'document_template_id' => 'nullable|exists:document_templates,id',
            'monthly_report_template_id' => 'nullable|exists:document_templates,id',
            'final_report_template_id' => 'nullable|exists:document_templates,id',
            'report_title' => 'nullable|string|max:255',
            'report_subtitle' => 'nullable|string|max:255',
            'report_header_html' => 'nullable|string',
            'report_footer_html' => 'nullable|string',
            'report_logo' => 'nullable|image|max:2048',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
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
