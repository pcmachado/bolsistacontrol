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
        $teacherPosition = Position::where('name', 'Professor')->first();

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

            $isTeacher = rand(0, 1); // 50%

            $position = $isTeacher && $teacherPosition
                ? $teacherPosition
                : $positions->random();

            $project->scholarshipHolders()->syncWithoutDetaching([
                $holder->id => [
                    'position_id' => $position->id,
                    'weekly_workload' => 20,
                    'edital_portaria' => 'Portaria XYZ/2026',
                    'start_date' => now()->subMonths(3)->toDateString(),
                    'end_date' => null,
                    'status' => 'active',
                ],
            ]);

            if ($position->is_teacher && $holder->user) {
                if (! $holder->user->hasRole('professor')) {
                    $holder->user->assignRole('professor');
                }
            }
        }
    }
}

