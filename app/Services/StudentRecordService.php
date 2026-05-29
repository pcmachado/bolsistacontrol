<?php

namespace App\Services;

use App\Models\ClassOffering;
use App\Models\StudentDisciplineMonthRecord;
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
            $query = StudentMonthRecord::query();

            $months = StudentDisciplineMonthRecord::where('student_id', $student->id)
                ->where('class_offering_id', $offering->id)
                ->get();

            $total = $months->sum('total_classes');

            $absences = $months->sum('absences');

            $attended = max(0, $total - $absences);

            $justified = $months->sum('justified_absences');

            $attended = max(0, $total - $absences);

            $frequency = $total > 0 ? ($attended / $total) : 0;

            $status = $frequency >= 0.75 ? 'approved' : 'failed';

            StudentRecord::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'class_offering_id' => $offering->id,
                ],
                [
                    'total_classes' => $totalClasses,
                    'total' => $total,
                    'justified' => $justified,
                    'absences' => $absences,
                    'attended_classes' => $attended,
                    'status' => $status,
                ]
            );
        }
    }
}
