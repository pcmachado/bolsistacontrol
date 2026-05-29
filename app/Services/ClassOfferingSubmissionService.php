<?php

namespace App\Services;

use App\Models\ClassOffering;
use App\Models\ClassOfferingSubmission;
use App\Models\StudentDisciplineMonthRecord;
use App\Models\User;
use Carbon\Carbon;

class ClassOfferingSubmissionService
{
    /*
    |--------------------------------------------------------------------------
    | Professor fecha o mês
    |--------------------------------------------------------------------------
    */

    public function submit(
        ClassOffering $class,
        int $disciplineId,
        int $month,
        int $year,
        User $user
    ): ClassOfferingSubmission {

        $records = StudentDisciplineMonthRecord::query()

            ->where(
                'class_offering_id',
                $class->id
            )

            ->where(
                'discipline_id',
                $disciplineId
            )

            ->where(
                'month',
                $month
            )

            ->where(
                'year',
                $year
            )

            ->get();

        if ($records->isEmpty()) {

            throw new \DomainException(
                'Não há registros lançados para este mês.'
            );
        }

        return ClassOfferingSubmission::updateOrCreate(

            [
                'class_offering_id' => $class->id,

                'discipline_id' => $disciplineId,

                'month' => $month,

                'year' => $year,
            ],

            [
                'total_students' =>
                    $records->count(),

                'status' => 'submitted',

                'submitted_by' =>
                    $user->id,

                'submitted_at' => now(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Aprovação
    |--------------------------------------------------------------------------
    */

    public function approve(
        ClassOfferingSubmission $submission,
        User $user
    ): void {

        $submission->update([

            'status' => 'approved',

            'approved_by' => $user->id,

            'approved_at' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Reabertura
    |--------------------------------------------------------------------------
    */

    public function reopen(
        ClassOfferingSubmission $submission
    ): void {

        $submission->update([

            'status' => 'reopened',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Rejeição
    |--------------------------------------------------------------------------
    */

    public function reject(
        ClassOfferingSubmission $submission,
        string $reason
    ): void {

        $submission->update([

            'status' => 'rejected',

            'rejected_reason' => $reason,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Pode editar?
    |--------------------------------------------------------------------------
    */

    public function canEdit(
        ClassOffering $class,
        int $disciplineId,
        int $month,
        int $year
    ): bool {

        $submission = $this->findSubmission(
            $class,
            $disciplineId,
            $month,
            $year
        );

        if (! $submission) {

            return true;
        }

        return in_array(
            $submission->status,
            ['draft', 'reopened', 'rejected']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Busca submissão
    |--------------------------------------------------------------------------
    */

    public function findSubmission(
        ClassOffering $class,
        int $disciplineId,
        int $month,
        int $year
    ): ?ClassOfferingSubmission {

        return ClassOfferingSubmission::query()

            ->where(
                'class_offering_id',
                $class->id
            )

            ->where(
                'discipline_id',
                $disciplineId
            )

            ->where(
                'month',
                $month
            )

            ->where(
                'year',
                $year
            )

            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Pode enviar mês?
    |--------------------------------------------------------------------------
    */

    public function canSubmitMonth(
        ClassOffering $class,
        int $disciplineId,
        int $month,
        int $year
    ): bool {

        $current = Carbon::create(
            $year,
            $month,
            1
        );

        $previous = $current
            ->copy()
            ->subMonth();

        $previousSubmission = $this->findSubmission(

            $class,

            $disciplineId,

            $previous->month,

            $previous->year
        );

        /*
        |--------------------------------------------------------------------------
        | Mês anterior precisa estar aprovado
        |--------------------------------------------------------------------------
        */

        if (
            $previousSubmission &&
            ! in_array(
                $previousSubmission->status,
                ['submitted', 'approved']
            )
        ) {

            return false;
        }

        return true;
    }
}