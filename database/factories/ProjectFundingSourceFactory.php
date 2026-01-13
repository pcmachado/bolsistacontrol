<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\FundingSource;

class ProjectFundingSourceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::inRandomOrder()->first()->id ?? Project::factory(),
            'funding_source_id' => FundingSource::inRandomOrder()->first()->id ?? FundingSource::factory(),
            'amount' => $this->faker->randomFloat(2, 5000, 20000),
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => null,
            'status' => 'active',
        ];
    }
}
