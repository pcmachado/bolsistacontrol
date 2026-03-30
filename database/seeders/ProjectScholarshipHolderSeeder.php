<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use Illuminate\Database\Seeder;

class ProjectScholarshipHolderSeeder extends Seeder
{
    public function run(): void
    {
        $holders = ScholarshipHolder::with('unit')->get();
        $positions = Position::all();

        if ($holders->isEmpty() || $positions->isEmpty()) {
            $this->command?->warn('ProjectScholarshipHolderSeeder: bolsistas/cargos ausentes.');
            return;
        }

        foreach ($holders as $holder) {
            $institutionId = $holder->unit?->institution_id;

            if ($institutionId) {
                $project = Project::where('institution_id', $institutionId)->inRandomOrder()->first();
            } else {
                $project = Project::inRandomOrder()->first();
            }

            if (! $project) {
                continue;
            }

            $positionId = $positions->random()->id;

            $project->scholarshipHolders()->syncWithoutDetaching([
                $holder->id => [
                    'position_id' => $positionId,
                    'weekly_workload' => 20,
                    'start_date' => now()->subMonths(3)->toDateString(),
                    'end_date' => null,
                    'status' => 'active',
                ],
            ]);
        }
    }
}

