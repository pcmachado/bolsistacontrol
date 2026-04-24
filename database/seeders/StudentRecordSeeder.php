<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\StudentRecord;

class StudentRecordSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::with('classOfferings.project')->get();

        foreach ($students as $student) {

            foreach ($student->classOfferings as $offering) {

                $totalClasses = rand(20, 40);
                $absences = rand(0, 5);

                $attended = $totalClasses - $absences;

                $rate = $offering->project->student_daily_rate ?? 50;

                StudentRecord::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'class_offering_id' => $offering->id,
                    ],
                    [
                        'total_classes' => $totalClasses,
                        'absences' => $absences,
                        'attended_classes' => $attended,
                        'daily_rate' => $rate,
                        'total_amount' => $attended * $rate,
                        'status' => $absences > 10 ? 'failed' : 'approved',
                    ]
                );
            }
        }
    }
}