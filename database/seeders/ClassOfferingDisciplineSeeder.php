<?php

namespace Database\Seeders;

use App\Models\ClassOffering;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassOfferingDisciplineSeeder extends Seeder
{
    public function run(): void
    {
        $teacherId = User::role('professor')->value('id');
        $offerings = ClassOffering::with('course.disciplines')->get();

        if ($offerings->isEmpty()) {
            $this->command?->warn('ClassOfferingDisciplineSeeder: nenhuma turma encontrada.');
            return;
        }

        foreach ($offerings as $offering) {
            $disciplines = $offering->course?->disciplines ?? collect();

            if ($disciplines->isEmpty()) {
                continue;
            }

            $sync = [];

            foreach ($disciplines as $discipline) {
                $sync[$discipline->id] = [
                    'teacher_id' => $teacherId,
                    'workload' => $discipline->workload ?: 30,
                    'schedule' => 'Seg/Qua 19:00-22:00',
                    'room' => 'Sala 101',
                ];
            }

            $offering->disciplines()->syncWithoutDetaching($sync);
        }
    }
}

