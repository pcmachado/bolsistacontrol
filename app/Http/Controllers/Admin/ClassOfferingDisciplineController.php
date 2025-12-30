<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\Discipline;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassOfferingDisciplineController extends Controller
{
    public function index(ClassOffering $offering)
    {
        // carrega disciplinas da turma + curso
        $offering->load([
            'disciplines',
            'course.disciplines',
        ]);

        return view('admin.class-offerings.disciplines.index', [
            'offering'    => $offering,
            'disciplines' => $offering->course->disciplines,
            'teachers'    => User::role('professor')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, ClassOffering $offering)
    {
        $validated = $request->validate([
            'disciplines' => ['nullable', 'array'],

            'disciplines.*.teacher_id' => ['nullable', 'exists:users,id'],
            'disciplines.*.workload'   => ['required', 'integer', 'min:1'],
            'disciplines.*.schedule'   => ['nullable', 'string', 'max:255'],
            'disciplines.*.room'       => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($offering, $validated) {
            $syncData = [];

            foreach ($validated['disciplines'] ?? [] as $disciplineId => $data) {
                $syncData[$disciplineId] = [
                    'teacher_id' => $data['teacher_id'] ?? null,
                    'workload'   => $data['workload'],
                    'schedule'   => $data['schedule'] ?? null,
                    'room'       => $data['room'] ?? null,
                ];
            }

            // sincroniza estado final da tela
            $offering->disciplines()->sync($syncData);
        });

        return redirect()
            ->route('admin.class-offerings.disciplines.index', $offering)
            ->with('success', 'Disciplinas da turma atualizadas com sucesso.');
    }
}
