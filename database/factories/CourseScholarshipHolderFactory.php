<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;
use App\Models\ScholarshipHolder;

class CourseScholarshipHolderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::inRandomOrder()->first()->id ?? Course::factory(),
            'scholarship_holder_id' => ScholarshipHolder::inRandomOrder()->first()->id ?? ScholarshipHolder::factory(),
        ];
    }
}
