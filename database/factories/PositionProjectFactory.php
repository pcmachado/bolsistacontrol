<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Position;
use App\Models\Project;

class PositionProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'position_id' => Position::inRandomOrder()->first()->id ?? Position::factory(),
            'project_id' => Project::inRandomOrder()->first()->id ?? Project::factory(),
            'assignments' => fake()->sentence(8),
            'hourly_rate' => fake()->randomFloat(2, 20, 120),
            'weekly_hour_limit' => fake()->numberBetween(10, 40),
        ];
    }
}
