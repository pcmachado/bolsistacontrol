<?php

namespace App\Services;

use App\Models\ClassOffering;
use App\Models\ClassOfferingSubmission;
use App\Models\StudentRecord;
use Carbon\Carbon;

class ClassOfferingSubmissionService
{
    public function createTeacherSubmission(ClassOffering $class, int $month, int $year): ClassOfferingSubmission
    {
        $totalStudents = $class->students()->count();

        return ClassOfferingSubmission::updateOrCreate(
            [
                'class_offering_id' => $class->id,
                'month' => $month,
                'year' => $year,
            ],
            [
                'total_students' => $totalStudents,
                'total_amount' => 0,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]
        );
    }

    public function submit(ClassOffering $class, int $month, int $year): ClassOfferingSubmission
    {
        if (! $this->hasTeacherSubmission($class, $month, $year)) {
            throw new \DomainException('O professor ainda não enviou a frequência mensal deste mês.');
        }

        $records = StudentRecord::where('class_offering_id', $class->id)->get();

        if ($records->isEmpty()) {
            throw new \DomainException('Não há lançamentos para enviar.');
        }

        $totalStudents = $records->count();
        $totalAmount = $records->sum('total_amount');

        return ClassOfferingSubmission::updateOrCreate(
            [
                'class_offering_id' => $class->id,
                'month' => $month,
                'year' => $year,
            ],
            [
                'total_students' => $totalStudents,
                'total_amount' => $totalAmount,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]
        );
    }

    public function canSubmitMonth(ClassOffering $class, int $month, int $year): bool
    {
        $current = Carbon::create($year, $month, 1);
        $previous = $current->copy()->subMonth();

        $previousSubmission = $class->submissions()
            ->where('month', $previous->month)
            ->where('year', $previous->year)
            ->first();

        if ($previousSubmission && ! in_array($previousSubmission->status, ['submitted', 'approved'])) {
            return false;
        }

        return $this->hasTeacherSubmission($class, $month, $year);
    }

    public function hasTeacherSubmission(ClassOffering $class, int $month, int $year): bool
    {
        return ClassOfferingSubmission::query()
            ->where('class_offering_id', $class->id)
            ->where('month', $month)
            ->where('year', $year)
            ->whereIn('status', ['submitted', 'approved'])
            ->exists();
    }
}
