<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Course;
use App\Models\ProjectCourse;

class ProjectCourseSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::first();
        $courses = Course::take(2)->get();

        foreach ($courses as $i => $course) {
            ProjectCourse::create([
                'project_id' => $project->id,
                'course_id' => $course->id,
                'semester' => 'Semester ' . ($i + 1),
                'year' => date('Y'),
                'active' => true,
                'start_date' => now()->subMonths(2),
                'end_date' => null,
                'capacity' => rand(20, 50),
                'status' => 'ongoing',
            ]);
        }
    }
}
