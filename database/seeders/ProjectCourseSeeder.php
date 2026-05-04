<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectCourseSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();

        if ($projects->isEmpty()) {
            $this->command?->warn('ProjectCourseSeeder: projetos ausentes.');

            return;
        }

        foreach ($projects as $project) {
            // ✅ Validar isolamento: apenas cursos da mesma instituição
            $courses = Course::where('institution_id', $project->institution_id)->get();

            if ($courses->isEmpty()) {
                $this->command?->warn("ProjectCourseSeeder: nenhum curso encontrado para instituição {$project->institution_id}.");

                continue;
            }

            $sample = $courses->shuffle()->take(min(3, $courses->count()));

            foreach ($sample as $course) {
                $project->courses()->syncWithoutDetaching([
                    $course->id => [
                        'active' => true,
                        'semester' => '1',
                        'year' => (int) now()->format('Y'),
                        'start_date' => $project->start_date,
                        'end_date' => $project->end_date,
                    ],
                ]);
            }
        }
    }
}
