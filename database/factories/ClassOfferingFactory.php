<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;
use App\Models\Project;
use App\Models\Unit;

class ClassOfferingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::inRandomOrder()->first()->id ?? Course::factory(),
            'project_id' => Project::inRandomOrder()->first()->id ?? Project::factory(),
            'unit_id' => Unit::inRandomOrder()->first()->id ?? Unit::factory(),
        ];
    }
}
