<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassOffering;
use Illuminate\Http\Request;
use App\DataTables\StudentDataTable;

class StudentController extends Controller
{
    public function index(Request $request, StudentDataTable $dataTable)
    {
        $filters = $request->only('class_offering_id');

        return $dataTable
            ->setFilters($filters)
            ->render('students.index', [
                'classes' => ClassOffering::orderBy('name')->get(),
                'classId' => $filters['class_offering_id'] ?? null,
            ]);
    }

    public function create()
    {
        $classes = ClassOffering::orderBy('name')->get();

        return view('students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_offering_id' => 'required|exists:class_offerings,id',
            'name' => 'required|string|max:255',
            'cpf' => 'nullable|string|max:14',
            'passport' => 'nullable|string|max:20',
            'payment_type' => 'required|in:pix,transfer',
            'pix_key' => 'nullable|string',
            'bank' => 'nullable|string',
            'agency' => 'nullable|string',
            'account' => 'nullable|string',
        ]);

        Student::create($data);

        return redirect()
            ->route('students.index')
            ->with('success', 'Aluno cadastrado com sucesso.');
    }

    public function edit(Student $student)
    {
        $classes = ClassOffering::orderBy('name')->get();

        return view('students.edit', compact('student','classes'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'class_offering_id' => 'required|exists:class_offerings,id',
            'name' => 'required|string|max:255',
            'cpf' => 'nullable|string|max:14',
            'passport' => 'nullable|string|max:20',
            'payment_type' => 'required|in:pix,transfer',
            'pix_key' => 'nullable|string',
            'bank' => 'nullable|string',
            'agency' => 'nullable|string',
            'account' => 'nullable|string',
        ]);

        $student->update($data);

        return redirect()
            ->route('students.index')
            ->with('success', 'Aluno atualizado.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return back()->with('success', 'Aluno removido.');
    }
}
