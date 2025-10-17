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
            'project_id' => Project::inRandomOrder()->first()->id ?? Project::factory(),
            'scholarship_holder_id' => ScholarshipHolder::inRandomOrder()->first()->id ?? ScholarshipHolder::factory(),
            'position_id' => Position::inRandomOrder()->first()->id ?? Position::factory(),
            'weekly_workload' => $this->faker->numberBetween(10, 20),
            'start_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'end_date' => null,
            'assignments' => $this->faker->sentence(),
            'hourly_rate' => $this->faker->randomFloat(2, 15, 50),
            'status' => 'active',
        ];
    }
}
