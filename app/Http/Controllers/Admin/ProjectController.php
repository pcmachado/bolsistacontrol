<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\institution;
use App\Models\Unit;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\DataTables\ProjectsDataTable;
use App\Services\ProjectService;

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
        $units = Unit::all();
        $institutions = institution::all();
        return view('admin.projects.create', compact('units', 'institutions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Project::create($validated);
        return redirect()->route('admin.projects.index')->with('success', 'Projeto criado com sucesso.');
    }

    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $units = Unit::all();
        $currentUnit = $project->unit_id ? Unit::find($project->unit_id) : null;

        $institutions = institution::all();
        $currentinstitution = $project->institution_id ? institution::find($project->institution_id) : null;

        return view('admin.projects.edit', compact('project', 'units', 'currentUnit', 'institutions', 'currentinstitution'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $project->update($validated);
        return redirect()->route('admin.projects.index')->with('success', 'Projeto atualizado com sucesso.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Projeto excluído com sucesso.');
    }
}
