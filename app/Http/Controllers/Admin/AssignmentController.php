<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Project;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Project $project)
    {
        $assignments = $project->assignments()->orderBy('created_at', 'desc')->get();
        return view('admin.assignments.index', compact('project', 'assignments'));
    }

    public function create(Project $project)
    {
        return view('admin.assignments.create', compact('project'));
    }

    public function show(Assignment $assignment)
    {
        return view('admin.assignments.show', compact('assignment'));
    }

    public function edit(Assignment $assignment)
    {
        return view('admin.assignments.edit', compact('assignment'));
    }

    
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'assignments' => 'required|array',
            'assignments.*.title' => 'required|string|max:255',
            'assignments.*.description' => 'nullable|string',
            'assignments.*.responsible_id' => 'nullable|exists:scholarship_holders,id',
        ]);

        foreach ($validated['assignments'] as $assignment) {
            $project->assignments()->create($assignment);
        }

        return redirect()->route('admin.projects.review', $project)
                         ->with('success', 'Atividades adicionadas com sucesso!');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return redirect()->route('admin.projects.review', $assignment->project)
                         ->with('success', 'Atividade removida com sucesso!');
    }
}
