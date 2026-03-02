<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Institution;
use App\Models\Unit;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\DataTables\ProjectsDataTable;
use App\Services\ProjectService;
use Illuminate\Support\Facades\Auth;

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

        $units = Unit::orderBy('name')->get();
        $institutions = $user->hasRole('admin')
            ? Institution::orderBy('name')->get()
            : Institution::where('id', $user->institution_id)->get();

        return view('admin.projects.create', compact('units', 'institutions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'institution_id' => 'required|exists:institutions,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if (! Auth::user()->hasRole('admin')) {
            $validated['institution_id'] = Auth::user()->institution_id;
        }

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

        $user = Auth::user();
        $units = Unit::orderBy('name')->get();
        $institutions = $user->hasRole('admin')
            ? Institution::orderBy('name')->get()
            : Institution::where('id', $user->institution_id)->get();

        return view('admin.projects.edit', compact('project', 'units', 'institutions'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'institution_id' => 'required|exists:institutions,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if (! Auth::user()->hasRole('admin')) {
            $validated['institution_id'] = Auth::user()->institution_id;
        }

        $project->update($validated);
        return redirect()->route('admin.projects.index')->with('success', 'Projeto atualizado com sucesso.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('view', $project);

        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Projeto excluído com sucesso.');
    }
}
