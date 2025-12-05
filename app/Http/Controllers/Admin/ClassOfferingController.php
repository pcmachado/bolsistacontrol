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
    public function index(ClassOfferingsDataTable $dataTable)
    {
        return $dataTable->render('admin.class_offerings.index');
    }

    public function create()
    {
        return view('admin.class_offerings.create', [
            'courses' => Course::orderBy('name')->get(),
            'units'   => Unit::orderBy('name')->get(),
            'projects'=> Project::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id'  => 'required|exists:courses,id',
            'unit_id'    => 'required|exists:units,id',
            'project_id' => 'nullable|exists:projects,id',
            'name'       => 'nullable|string|max:255',
            'semester'   => 'nullable|string|max:20',
            'year'       => 'nullable|numeric|min:2000',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'capacity'   => 'nullable|integer|min:1',
            'status'     => 'required|string',
        ]);

        $offering = ClassOffering::create($validated);

        return redirect()
            ->route('admin.class-offerings.index')
            ->with('success', 'Turma criada com sucesso!');
    }

    public function edit(ClassOffering $classOffering)
    {
        return view('admin.class_offerings.edit', [
            'offering'  => $classOffering,
            'courses'   => Course::orderBy('name')->get(),
            'units'     => Unit::orderBy('name')->get(),
            'projects'  => Project::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ClassOffering $classOffering)
    {
        $validated = $request->validate([
            'course_id'  => 'required|exists:courses,id',
            'unit_id'    => 'required|exists:units,id',
            'project_id' => 'nullable|exists:projects,id',
            'name'       => 'nullable|string|max:255',
            'semester'   => 'nullable|string|max:20',
            'year'       => 'nullable|numeric|min:2000',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'capacity'   => 'nullable|integer|min:1',
            'status'     => 'required|string',
        ]);

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
