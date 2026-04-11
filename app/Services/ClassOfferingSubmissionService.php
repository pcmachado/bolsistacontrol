<?php

namespace App\Services;

use App\Models\ClassOffering;
use App\Models\ClassOfferingSubmission;
use App\Models\StudentRecord;
use Carbon\Carbon;

class ClassOfferingSubmissionService
{
    public function submit(ClassOffering $class, int $month, int $year): ClassOfferingSubmission
    {
        $records = StudentRecord::where('class_offering_id', $class->id)->get();

        if ($records->isEmpty()) {
            throw new \DomainException('Não há lançamentos para enviar.');
        }

        $totalStudents = $records->count();
        $totalAmount = $records->sum('total_amount');

        $submission = ClassOfferingSubmission::updateOrCreate(
            [
                'class_offering_id' => $class->id,
                'month' => $month,
                'year' => $year
            ],
            [
                'total_students' => $totalStudents,
                'total_amount' => $totalAmount,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]
        );

        return $submission;
    }

    public function canSubmitMonth(ClassOffering $class, int $month, int $year): bool
    {
        $current = Carbon::create($year, $month, 1);
        $previous = $current->copy()->subMonth();

        $previousSubmission = $class->submissions()
            ->where('month', $previous->month)
            ->where('year', $previous->year)
            ->first();

        if (!$previousSubmission) {
            return true;
        }

        return in_array($previousSubmission->status, ['submitted', 'approved']);
    }
}