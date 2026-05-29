<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\ClassOfferingDiscipline;
use App\Models\Discipline;
use App\Models\ScholarshipHolder;
use App\Models\StudentRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassOfferingDisciplineController extends Controller
{
    public function index(ClassOffering $offering)
    {
        $offering->load([
            'disciplines',
            'course.disciplines',
        ]);

        $pendingTeachers = $offering->classOfferingDisciplines()
            ->with(['discipline:id,name', 'teacher.user'])
            ->get()
            ->filter(fn ($item) => ! $item->teacher_id)
            ->values();

        $recordedTeachers = $offering->classOfferingDisciplines()
            ->whereNotNull('teacher_id')
            ->distinct('teacher_id')
            ->count('teacher_id');

        $recordsSummary = StudentRecord::query()
            ->where('class_offering_id', $offering->id)
            ->selectRaw('COUNT(*) as total_records, COALESCE(SUM(total_amount),0) as projected_total')
            ->first();

        return view(
            'admin.class-offerings.disciplines.index',
            [

                'offering' => $offering,

                'disciplines' => $offering
                    ->course
                    ->disciplines,

                'teachers' => ScholarshipHolder::query()

                    ->with('user')

                    ->whereExists(function ($subQuery) {

                        $subQuery->select(DB::raw(1))

                            ->from(
                                'project_scholarship_holder as psh'
                            )

                            ->join(
                                'positions as p',
                                'p.id',
                                '=',
                                'psh.position_id'
                            )

                            ->whereColumn(
                                'psh.scholarship_holder_id',
                                'scholarship_holders.id'
                            )

                            ->where(
                                'p.is_teacher',
                                true
                            );
                    })

                    ->orderBy('name')

                    ->get(),

                'pendingTeachers' => $pendingTeachers,
                'recordedTeachers' => $recordedTeachers,
                'recordsSummary' => $recordsSummary,
            ]
        );
    }

    public function store(
        Request $request,
        ClassOffering $offering
    ) {

        $validated = $request->validate([

            'discipline_id' => [
                'required',
                'integer',
                'exists:disciplines,id',
            ],

            'teacher_id' => [
                'nullable',
                'integer',
                'exists:scholarship_holders,id',
            ],

            'schedule' => [
                'nullable',
                'string',
                'max:255',
            ],

            'room' => [
                'nullable',
                'string',
                'max:255',
            ],

            'hours_per_day' => [
                'required',
                'numeric',
                'min:0.25',
                'max:24',
            ],
        ]);

        $isCourseDiscipline = $offering
            ->course
            ->disciplines()
            ->where(
                'disciplines.id',
                $validated['discipline_id']
            )
            ->exists();

        if (! $isCourseDiscipline) {

            return back()
                ->withInput()
                ->withErrors([
                    'discipline_id' =>
                        'A disciplina selecionada não pertence ao curso desta turma.'
                ]);
        }

        $discipline = Discipline::query()
            ->findOrFail(
                $validated['discipline_id']
            );

        $validated['workload'] = max(
            1,
            (int) ($discipline->workload ?? 1)
        );

        $validated['planned_total_hours'] = $validated['workload'];

        DB::transaction(function () use (
            $offering,
            $validated
        ) {

            $offering
                ->disciplines()
                ->syncWithoutDetaching([

                    $validated['discipline_id'] => [

                        'teacher_id' =>
                            $validated['teacher_id']
                            ?? null,

                        'workload' =>
                            $validated['workload'],

                        'planned_total_hours' =>
                            $validated['planned_total_hours'],

                        'hours_per_day' =>
                            $validated['hours_per_day'],

                        'schedule' =>
                            $validated['schedule']
                            ?? null,

                        'room' =>
                            $validated['room']
                            ?? null,
                    ],
                ]);
        });

        return redirect()
            ->route(
                'admin.class-offerings.disciplines.index',
                $offering
            )
            ->with(
                'success',
                'Disciplina vinculada/atualizada com sucesso.'
            );
    }

    public function update(
        Request $request,
        ClassOfferingDiscipline $pivot
    ) {

        $validated = $request->validate([

            'teacher_id' => [
                'nullable',
                'integer',
                'exists:scholarship_holders,id',
            ],

            'workload' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'schedule' => [
                'nullable',
                'string',
                'max:255',
            ],

            'room' => [
                'nullable',
                'string',
                'max:255',
            ],

            'hours_per_day' => [
                'nullable',
                'numeric',
                'min:0.25',
                'max:24',
            ],
        ]);

        if (array_key_exists('workload', $validated)) {
            $validated['planned_total_hours'] = $validated['workload'];
        }

        $pivot->update($validated);

        return redirect()
            ->route(
                'admin.class-offerings.disciplines.index',
                $pivot->class_offering_id
            )
            ->with(
                'success',
                'Disciplina da turma atualizada com sucesso.'
            );
    }

    public function destroy(
        ClassOfferingDiscipline $pivot
    ) {

        $offeringId = $pivot->class_offering_id;

        $pivot->delete();

        return redirect()
            ->route(
                'admin.class-offerings.disciplines.index',
                $offeringId
            )
            ->with(
                'success',
                'Disciplina removida da turma com sucesso.'
            );
    }
}
