<?php

namespace Database\Factories;

use App\Models\Instituition;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'instituition_id' => Instituition::factory(),
            'name' => $this->faker->word(),
            'city' => $this->faker->city(),
            'address' => $this->faker->address(),
        ];
    }
}
