<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discipline;
use App\Models\Course;
use App\DataTables\DisciplinesDataTable;
use Illuminate\Http\Request;

class DisciplineController extends Controller
{
    public function index(DisciplinesDataTable $dataTable)
    {
        return $dataTable->render('admin.disciplines.index');
    }

    public function create()
    {
        $courses = Course::orderBy('name')->get();

        return view('admin.disciplines.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id'      => 'required|exists:courses,id',
            'name'           => 'required|string|max:255',
            'workload'       => 'nullable|integer|min:1',
            'sequence_order' => 'nullable|integer|min:1',
        ]);

        Discipline::create($validated);

        return redirect()
            ->route('admin.disciplines.index')
            ->with('success', 'Disciplina cadastrada com sucesso!');
    }

    public function edit(Discipline $discipline)
    {
        $courses = Course::orderBy('name')->get();

        return view('admin.disciplines.edit', compact('discipline', 'courses'));
    }

    public function update(Request $request, Discipline $discipline)
    {
        $validated = $request->validate([
            'course_id'      => 'required|exists:courses,id',
            'name'           => 'required|string|max:255',
            'workload'       => 'nullable|integer|min:1',
            'sequence_order' => 'nullable|integer|min:1',
        ]);

        $discipline->update($validated);

        return redirect()
            ->route('admin.disciplines.index')
            ->with('success', 'Disciplina atualizada com sucesso!');
    }

    public function destroy(Discipline $discipline)
    {
        $discipline->delete();

        return redirect()
            ->route('admin.disciplines.index')
            ->with('success', 'Disciplina removida!');
    }
}
