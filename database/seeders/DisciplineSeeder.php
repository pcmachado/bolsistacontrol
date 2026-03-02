<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Discipline;
use Illuminate\Database\Seeder;

class DisciplineSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();

        if ($courses->isEmpty()) {
            $this->command?->warn('DisciplineSeeder: nenhum curso encontrado.');
            return;
        }

        foreach ($courses as $course) {
            $base = [
                ['name' => 'Fundamentos', 'workload' => 60, 'sequence_order' => 1],
                ['name' => 'Pratica de Projeto', 'workload' => 60, 'sequence_order' => 2],
                ['name' => 'Seminarios', 'workload' => 30, 'sequence_order' => 3],
            ];

            foreach ($base as $item) {
                Discipline::firstOrCreate(
                    [
                        'course_id' => $course->id,
                        'name' => $item['name'],
                    ],
                    [
                        'workload' => $item['workload'],
                        'sequence_order' => $item['sequence_order'],
                        'active' => true,
                    ]
                );
            }
        }
    }
}

