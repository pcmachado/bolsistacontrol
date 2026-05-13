<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\Course;
use App\Models\Unit;
use App\Models\Project;
use App\DataTables\ClassOfferingsDataTable;
use Illuminate\Http\Request;

class ClassOfferingController extends Controller
{
    public function index(Request $request, ClassOfferingsDataTable $dataTable)
    {
        $filters = $request->only([
            'filter_course',
            'filter_unit',
            'filter_project',
            'filter_status',
            'filter_year',
            'filter_semester',
            'filter_min_students',
        ]);

        return $dataTable
            ->setFilters($filters)
            ->render('admin.class-offerings.index');
    }

    public function create()
    {
        $projects = Project::with([
            'courses' => fn ($q) => $q->wherePivot('active', true)->orderBy('name'),
        ])->orderBy('name')->get();

        $projectCourses = $projects->mapWithKeys(fn ($project) => [
            $project->id => $project->courses->map(fn ($course) => [
                'id' => $course->id,
                'name' => $course->name,
                'capacity' => $course->capacity,
            ])->values(),
        ]);

        return view('admin.class-offerings.create', [
            'courses' => Course::orderBy('name')->get(),
            'units'   => Unit::orderBy('name')->get(),
            'projects'=> $projects,
            'projectCourses' => $projectCourses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id'  => 'required|exists:courses,id',
            'unit_id'    => 'required|exists:units,id',
            'project_id' => 'required|exists:projects,id',
            'name'       => 'nullable|string|max:255',
            'semester'   => 'nullable|string|max:20',
            'year'       => 'nullable|numeric|min:2000',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'capacity'   => 'nullable|integer|min:1',
            'status'     => 'required|string',
        ]);

        $project = Project::with([
            'courses' => fn ($q) => $q->wherePivot('active', true),
        ])->findOrFail((int) $validated['project_id']);

        if (! $project->courses->contains('id', (int) $validated['course_id'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'course_id' => 'O curso selecionado nao esta vinculado ao projeto informado.',
                ]);
        }

        if (empty($validated['capacity'])) {
            $validated['capacity'] = $project->courses
                ->firstWhere('id', (int) $validated['course_id'])
                ?->capacity;
        }

        $offering = ClassOffering::create($validated);

        return redirect()
            ->route('admin.class-offerings.index')
            ->with('success', 'Turma criada com sucesso!');
    }

    public function edit(ClassOffering $classOffering)
    {
        $projects = Project::with([
            'courses' => fn ($q) => $q->wherePivot('active', true)->orderBy('name'),
        ])->orderBy('name')->get();

        $projectCourses = $projects->mapWithKeys(fn ($project) => [
            $project->id => $project->courses->map(fn ($course) => [
                'id' => $course->id,
                'name' => $course->name,
                'capacity' => $course->capacity,
            ])->values(),
        ]);

        return view('admin.class-offerings.edit', [
            'offering'  => $classOffering,
            'courses'   => Course::orderBy('name')->get(),
            'units'     => Unit::orderBy('name')->get(),
            'projects'  => $projects,
            'projectCourses' => $projectCourses,
        ]);
    }

    public function update(Request $request, ClassOffering $classOffering)
    {
        $validated = $request->validate([
            'course_id'  => 'required|exists:courses,id',
            'unit_id'    => 'required|exists:units,id',
            'project_id' => 'required|exists:projects,id',
            'name'       => 'nullable|string|max:255',
            'semester'   => 'nullable|string|max:20',
            'year'       => 'nullable|numeric|min:2000',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'capacity'   => 'nullable|integer|min:1',
            'status'     => 'required|string',
        ]);

        $project = Project::with([
            'courses' => fn ($q) => $q->wherePivot('active', true),
        ])->findOrFail((int) $validated['project_id']);

        if (! $project->courses->contains('id', (int) $validated['course_id'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'course_id' => 'O curso selecionado nao esta vinculado ao projeto informado.',
                ]);
        }

        $classOffering->update($validated);

        return redirect()
            ->route('admin.class-offerings.index')
            ->with('success', 'Turma atualizada com sucesso!');
    }

    public function destroy(ClassOffering $classOffering)
    {
        $classOffering->delete();

        return redirect()
            ->route('admin.class-offerings.index')
            ->with('success', 'Turma removida!');
    }
}
