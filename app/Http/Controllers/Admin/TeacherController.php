<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\ScholarshipHolder;
use App\DataTables\TeachersDataTable;
use App\Services\VisibilityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class TeacherController extends Controller
{
    public function index(Request $request, TeachersDataTable $dataTable)
    {
        $user = Auth::user();
        $filters = $request->only([
            'filter_unit',
            'filter_course',
            'filter_offering',
        ]);

        $offerings = app(VisibilityService::class)
            ->apply(ClassOffering::query(), $user, 'admin')
            ->orderBy('name')
            ->get();

        return $dataTable
            ->setFilters($filters)
            ->render('admin.teachers.index', compact('offerings'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = ScholarshipHolder::create($validated);

        $user->assignRole('professor');

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Professor cadastrado com sucesso!');
    }

    public function edit(ScholarshipHolder $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, ScholarshipHolder $teacher)
    {
        $validated = $request->validate([
            'name'     => 'required|max:255',
            'email'    => 'required|email|unique:users,email,' . $teacher->id,
            'password' => 'nullable|min:6',
        ]);

        if ($validated['password'] ?? false) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $teacher->update($validated);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Professor atualizado com sucesso!');
    }

    public function destroy(ScholarshipHolder $teacher)
    {
        $teacher->delete();

        return back()->with('success', 'Professor removido!');
    }
}
