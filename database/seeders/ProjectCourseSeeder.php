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
        $courses = Course::all();

        if ($projects->isEmpty() || $courses->isEmpty()) {
            $this->command?->warn('ProjectCourseSeeder: projetos ou cursos ausentes.');
            return;
        }

        foreach ($projects as $project) {
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

