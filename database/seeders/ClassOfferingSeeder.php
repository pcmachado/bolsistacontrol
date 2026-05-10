<?php

namespace Database\Seeders;

use App\Models\ClassOffering;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ClassOfferingSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::with('courses')->get();
        $units = Unit::all();

        if ($projects->isEmpty() || $units->isEmpty()) {
            $this->command?->warn('ClassOfferingSeeder: projetos/unidades ausentes.');

            return;
        }

        foreach ($projects as $project) {
            $projectCourses = $project->courses->where('pivot.active', true)->values();

            if ($projectCourses->isEmpty()) {
                continue;
            }

            $sampleCourses = $projectCourses->take(min(2, $projectCourses->count()));
            $unit = $units->firstWhere('institution_id', $project->institution_id) ?? $units->first();

            foreach ($sampleCourses as $index => $course) {
                $start = now()->copy()->startOfYear();
                $end = now()->copy()->endOfMonth();

                ClassOffering::updateOrCreate(
                    [
                        'project_id' => $project->id,
                        'course_id' => $course->id,
                        'unit_id' => $unit->id,
                        'year' => now()->year,
                        'semester' => now()->format('Y').'/'.($index + 1),
                    ],
                    [
                        'name' => "Turma {$project->id}-{$course->id}",
                        'active' => true,
                        'start_date' => $start->toDateString(),
                        'end_date' => $end->toDateString(),
                        'capacity' => rand(20, 40),
                        'status' => 'ongoing',
                    ]
                );
            }
        }
    }
}
