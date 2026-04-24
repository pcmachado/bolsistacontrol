<?php

namespace App\Services;

use App\Models\ClassOffering;
use App\Models\StudentMonthRecord;
use App\Models\StudentRecord;

class StudentRecordService
{
    public function generate(ClassOffering $offering): void
    {
        $course = $offering->course;

        $totalClasses = $course->duration_hours ?? 0;

        $students = $offering->students;

        foreach ($students as $student) {

            $months = StudentMonthRecord::where([
                'student_id' => $student->id,
                'class_offering_id' => $offering->id,
            ])->get();

            $absences = $months->sum('absences');

            $attended = max(0, $totalClasses - $absences);

            $frequency = $totalClasses > 0
                ? ($attended / $totalClasses)
                : 0;

            $status = $frequency >= 0.75 ? 'approved' : 'failed';

            StudentRecord::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'class_offering_id' => $offering->id,
                ],
                [
                    'total_classes' => $totalClasses,
                    'absences' => $absences,
                    'attended_classes' => $attended,
                    'status' => $status,
                ]
            );
        }
    }
}