<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectScholarshipHolderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'scholarship_holder_id' => ScholarshipHolder::factory(),
            'position_id' => Position::factory(),
            'monthly_workload' => $this->faker->randomFloat(1, 10, 40),
            'start_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
