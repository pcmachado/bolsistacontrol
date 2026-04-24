<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ClassOffering;
use App\Models\Discipline;
use App\Models\TeachingAssignment;

class TeachingAssignmentsSeeder extends Seeder
{
    public function run(): void
    {
        // pega docentes
        $teachers = User::role('professor')->get();

        $offerings = ClassOffering::all();
        $disciplines = Discipline::all();

        if ($teachers->isEmpty() || $offerings->isEmpty()) {
            $this->command->warn('Sem dados para TeachingAssignmentsSeeder');
            return;
        }

        foreach ($offerings as $offering) {

            $teacher = $teachers->random();
            $discipline = $disciplines->random();

            TeachingAssignment::updateOrCreate([
                'class_offering_id' => $offering->id,
            ], [
                'teacher_id' => $teacher->id,
                'discipline_id' => $discipline->id,
            ]);
        }
    }
}