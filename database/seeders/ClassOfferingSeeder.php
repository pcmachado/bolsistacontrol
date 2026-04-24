<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassOffering;
use App\Models\Project;
use App\Models\Unit;
use Carbon\Carbon;

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

                $year = now()->year;

                // 👇 início em janeiro (ou aleatório no 1º semestre)
                $start = Carbon::create($year, rand(1, 3), 1);

                // 👇 duração de 5 a 6 meses
                $durationMonths = rand(5, 6);

                $end = $start->copy()->addMonths($durationMonths)->subDay();

                ClassOffering::firstOrCreate(
                    [
                        'project_id' => $project->id,
                        'course_id' => $course->id,
                        'unit_id' => $unit->id,
                        'year' => (int) now()->format('Y'),
                        'semester' => '2026/' . ($index + 1),
                    ],
                    [
                        'name' => "Turma {$project->id}-{$course->id}",
                        'active' => true,
                        'start_date' => $start,
                        'end_date' => $end,
                        'capacity' => rand(20, 40),
                        'status' => 'ongoing',
                    ]
                );
            }
        }
    }
}
