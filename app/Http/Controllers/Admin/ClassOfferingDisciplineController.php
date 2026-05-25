<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\ClassOfferingDiscipline;
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
            'discipline_id' => ['required', 'integer', 'exists:disciplines,id'],
            'teacher_id'    => ['nullable', 'integer', 'exists:users,id'],
            'schedule'      => ['nullable', 'string', 'max:255'],
            'room'          => ['nullable', 'string', 'max:255'],
        ]);

        $isCourseDiscipline = $offering->course
            ->disciplines()
            ->where('disciplines.id', $validated['discipline_id'])
            ->exists();


        $discipline = Discipline::query()->findOrFail($validated['discipline_id']);
        $validated['workload'] = max(1, (int) ($discipline->workload ?? 1));

        if (! $isCourseDiscipline) {
            return back()
                ->withInput()
                ->withErrors(['discipline_id' => 'A disciplina selecionada nao pertence ao curso desta turma.']);
        }

        DB::transaction(function () use ($offering, $validated) {
            $offering->disciplines()->syncWithoutDetaching([
                $validated['discipline_id'] => [
                    'teacher_id' => $validated['teacher_id'] ?? null,
                    'workload'   => $validated['workload'],
                    'schedule'   => $validated['schedule'] ?? null,
                    'room'       => $validated['room'] ?? null,
                ],
            ]);
        });

        return redirect()
            ->route('admin.class-offerings.disciplines.index', $offering)
            ->with('success', 'Disciplina vinculada/atualizada com sucesso.');
    }

    public function update(Request $request, ClassOfferingDiscipline $pivot)
    {
        $validated = $request->validate([
            'teacher_id' => ['nullable', 'integer', 'exists:users,id'],
            'workload'   => ['nullable', 'integer', 'min:1'],
            'schedule'   => ['nullable', 'string', 'max:255'],
            'room'       => ['nullable', 'string', 'max:255'],
        ]);

        $pivot->update($validated);

        return redirect()
            ->route('admin.class-offerings.disciplines.index', $pivot->class_offering_id)
            ->with('success', 'Disciplina da turma atualizada com sucesso.');
    }

    public function destroy(ClassOfferingDiscipline $pivot)
    {
        $offeringId = $pivot->class_offering_id;
        $pivot->delete();

        return redirect()
            ->route('admin.class-offerings.disciplines.index', $offeringId)
            ->with('success', 'Disciplina removida da turma com sucesso.');
    }
}
