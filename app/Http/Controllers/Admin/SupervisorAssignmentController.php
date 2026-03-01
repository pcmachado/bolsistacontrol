<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupervisorAssignment;
use App\Models\User;
use App\Models\Course;
use App\Models\Unit;
use App\DataTables\SupervisorsDataTable;
use Illuminate\Http\Request;

class SupervisorAssignmentController extends Controller
{
    public function index(Request $request, SupervisorsDataTable $dataTable)
    {
        $filters = $request->only([
            'filter_unit',
            'filter_course',
            'filter_status',
        ]);

        return $dataTable
            ->setFilters($filters)
            ->render('admin.supervisors.index');
    }

    public function create()
    {
        return view('admin.supervisors.create', [
            'supervisors' => User::role('supervisor')->orderBy('name')->get(),
            'courses'     => Course::orderBy('name')->get(),
            'units'       => Unit::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supervisor_id' => 'required|exists:users,id',
            'course_id'     => 'required|exists:courses,id',
            'unit_id'       => 'required|exists:units,id',
        ]);

        // Evitar duplicação
        $exists = SupervisorAssignment::where($validated)->exists();
        if ($exists) {
            return back()->with('warning', 'Este vínculo já existe.');
        }

        SupervisorAssignment::create($validated);

        return redirect()->route('admin.supervisors.index')
            ->with('success', 'Supervisor vinculado com sucesso!');
    }

    public function edit(SupervisorAssignment $supervisorAssignment)
    {
        return view('admin.supervisors.edit', [
            'assignment'  => $supervisorAssignment,
            'supervisors' => User::role('supervisor')->orderBy('name')->get(),
            'courses'     => Course::orderBy('name')->get(),
            'units'       => Unit::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, SupervisorAssignment $supervisorAssignment)
    {
        $validated = $request->validate([
            'supervisor_id' => 'required|exists:users,id',
            'course_id'     => 'required|exists:courses,id',
            'unit_id'       => 'required|exists:units,id',
            'active'        => 'required|boolean',
        ]);

        $supervisorAssignment->update($validated);

        return redirect()->route('admin.supervisors.index')
            ->with('success', 'Vínculo atualizado!');
    }

    public function destroy(SupervisorAssignment $supervisorAssignment)
    {
        $supervisorAssignment->delete();

        return back()->with('success', 'Vínculo removido!');
    }
}
