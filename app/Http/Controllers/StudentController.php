<?php

namespace App\Http\Controllers;

use App\DataTables\StudentDataTable;
use App\Models\ClassOffering;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(
        Request $request,
        StudentDataTable $dataTable
    )
    {
        $filters = $request->only(
            'class_offering_id'
        );

        return $dataTable
            ->setFilters($filters)
            ->render(
                'admin.students.index',
                [
                    'classes' => ClassOffering::query()
                        ->orderBy('name')
                        ->get(),

                    'classId' => $filters['class_offering_id']
                        ?? null,
                ]
            );
    }

    public function create(): View
    {
        $classes = ClassOffering::query()
            ->orderBy('name')
            ->get();

        return view(
            'admin.students.create',
            compact('classes')
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {

        $data = $request->validate([

            'class_offering_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'class_offering_ids.*' => [
                'exists:class_offerings,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'cpf' => [
                'nullable',
                'string',
                'max:14',
            ],

            'passport' => [
                'nullable',
                'string',
                'max:20',
            ],

            'payment_type' => [
                'required',
                'in:pix,transfer',
            ],

            'pix_key' => [
                'required_if:payment_type,pix',
                'nullable',
                'string',
            ],

            'bank' => [
                'required_if:payment_type,transfer',
                'nullable',
                'string',
            ],

            'agency' => [
                'required_if:payment_type,transfer',
                'nullable',
                'string',
            ],

            'account' => [
                'required_if:payment_type,transfer',
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use ($data) {

            $classOfferingIds = $data['class_offering_ids'];

            unset($data['class_offering_ids']);

            $student = Student::create($data);

            $student->classOfferings()->sync(
                $classOfferingIds
            );
        });

        return redirect()
            ->route('admin.students.index')
            ->with(
                'success',
                'Aluno cadastrado com sucesso.'
            );
    }

    public function edit(
        Student $student
    ): View {

        $classes = ClassOffering::query()
            ->orderBy('name')
            ->get();

        $selectedClasses = $student
            ->classOfferings()
            ->pluck('class_offerings.id')
            ->toArray();

        return view(
            'admin.students.edit',
            compact(
                'student',
                'classes',
                'selectedClasses'
            )
        );
    }

    public function update(
        Request $request,
        Student $student
    ): RedirectResponse {

        $data = $request->validate([

            'class_offering_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'class_offering_ids.*' => [
                'exists:class_offerings,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'cpf' => [
                'nullable',
                'string',
                'max:14',
            ],

            'passport' => [
                'nullable',
                'string',
                'max:20',
            ],

            'payment_type' => [
                'required',
                'in:pix,transfer',
            ],

            'pix_key' => [
                'required_if:payment_type,pix',
                'nullable',
                'string',
            ],

            'bank' => [
                'required_if:payment_type,transfer',
                'nullable',
                'string',
            ],

            'agency' => [
                'required_if:payment_type,transfer',
                'nullable',
                'string',
            ],

            'account' => [
                'required_if:payment_type,transfer',
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use (
            $student,
            $data
        ) {

            $classOfferingIds = $data['class_offering_ids'];

            unset($data['class_offering_ids']);

            $student->update($data);

            $student->classOfferings()->sync(
                $classOfferingIds
            );
        });

        return redirect()
            ->route('admin.students.index')
            ->with(
                'success',
                'Aluno atualizado.'
            );
    }

    public function destroy(
        Student $student
    ): RedirectResponse {

        DB::transaction(function () use (
            $student
        ) {

            $student->classOfferings()
                ->detach();

            $student->delete();
        });

        return back()->with(
            'success',
            'Aluno removido.'
        );
    }
}