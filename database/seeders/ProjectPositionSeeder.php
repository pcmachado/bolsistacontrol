<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProjectPosition;
use App\Models\Project;
use App\Models\Position;
use App\Models\ScholarshipHolder;

class ProjectPositionSeeder extends Seeder
{
    public function run(): void
    {
        // Garante que já existem projetos e posições
        $projects  = Project::all();
        $positions = Position::all();

        foreach ($projects as $project) {
            foreach ($positions as $position) {
                $project->positions()->syncWithoutDetaching([
                    $position->id => [
                        'hourly_rate' => fake()->randomFloat(2, 20, 80), // valor/hora aleatório
                    ],
                ]);
            }
        }
    }
}
