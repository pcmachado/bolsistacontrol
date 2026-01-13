<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FundingSourceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Foundation',
            'type' => $this->faker->randomElement(['internal', 'external']),
        ];
    }
}
