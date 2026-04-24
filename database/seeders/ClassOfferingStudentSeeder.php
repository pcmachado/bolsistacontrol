<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassOffering;
use App\Models\Student;

class ClassOfferingStudentSeeder extends Seeder
{
    public function run(): void
    {
        $offerings = ClassOffering::all();
        $students = Student::all();

        foreach ($offerings as $offering) {

            // pega aleatório
            $selected = $students->random(min(15, $students->count()));

            $syncData = [];

            foreach ($selected as $student) {
                $syncData[$student->id] = [];
            }

            $offering->students()->syncWithoutDetaching($syncData);
        }
    }
}