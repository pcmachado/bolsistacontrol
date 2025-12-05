<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Course;
use App\Models\Unit;
use App\Models\ClassOffering;

class ClassOfferingSeeder extends Seeder
{
    public function run(): void
    {
        /*$project = Project::first();
        $courses = Course::take(2)->get();

        foreach ($courses as $i => $course) {
            ClassOffering::create([
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
        }*/

            ClassOffering::insert([
            [
                'project_id' => 1,
                'course_id' => 1,
                'unit_id' => 2,
                'semester' => 'Semester ' . 1,
                'year' => date('Y'),
                'active' => true,
                'start_date' => now()->subMonths(2),
                'end_date' => null,
                'capacity' => rand(20, 50),
                'status' => 'ongoing',
            ],
            [
                'project_id' => 1,
                'course_id' => 2,
                'unit_id' => 1,
                'semester' => 'Semester ' . 1,
                'year' => date('Y'),
                'active' => true,
                'start_date' => now()->subMonths(2),
                'end_date' => null,
                'capacity' => rand(20, 50),
                'status' => 'ongoing',
            ],

            [
                'project_id' => 2,
                'course_id' => 3,
                'unit_id' => 2,
                'semester' => 'Semester ' . 2,
                'year' => date('Y'),
                'active' => true,
                'start_date' => now()->subMonths(2),
                'end_date' => null,
                'capacity' => rand(20, 50),
                'status' => 'ongoing',
            ],

            [
                'project_id' => 3,
                'course_id' => 4,
                'unit_id' => 6,
                'semester' => 'Semester ' . 1,
                'year' => date('Y'),
                'active' => true,
                'start_date' => now()->subMonths(2),
                'end_date' => null,
                'capacity' => rand(20, 50),
                'status' => 'ongoing',
            ],

            [
                'project_id' => 4,
                'course_id' => 5,
                'unit_id' => 6,
                'semester' => 'Semester ' . 2,
                'year' => date('Y'),
                'active' => true,
                'start_date' => now()->subMonths(2),
                'end_date' => null,
                'capacity' => rand(20, 50),
                'status' => 'ongoing',
            ],
        ]);
    }
}
