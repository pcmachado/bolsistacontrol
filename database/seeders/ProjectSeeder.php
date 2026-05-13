<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $institutions = Institution::all();

        foreach ($institutions as $institution) {
            for ($i = 1; $i <= 2; $i++) {
                Project::updateOrCreate(
                    [
                        'name' => "{$institution->name} - Projeto {$i}",
                        'institution_id' => $institution->id,
                    ],
                    [
                        'description' => "Projeto {$i} da {$institution->name}",
                        'student_daily_rate' => 5.00,
                        'status' => Project::STATUS_ACTIVE,
                        'wizard_step' => 'completed',
                        'start_date' => now()->startOfYear()->toDateString(),
                        'end_date' => null,
                    ]
                );
            }
        }
    }
}
