<?php

namespace Database\Seeders;

use App\Models\ClassOffering;
use App\Models\Discipline;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeachingAssignmentsSeeder extends Seeder
{
    public function run(): void
    {
        // pega professores
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
