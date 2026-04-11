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
        $students = Student::with('classOffering.project')->get();

        foreach ($students as $student) {

            $totalClasses = rand(20, 40);
            $absences = rand(0, 5);

            $attended = $totalClasses - $absences;

            $rate = $student->classOffering->project->student_daily_rate ?? 50;

            StudentRecord::create([
                'student_id' => $student->id,
                'class_offering_id' => $student->class_offering_id,
                'total_classes' => $totalClasses,
                'absences' => $absences,
                'attended_classes' => $attended,
                'daily_rate' => $rate,
                'total_amount' => $attended * $rate,
                'status' => $absences > 10 ? 'failed' : 'approved',
            ]);
        }
    }
}