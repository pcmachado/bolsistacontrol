<?php

namespace Database\Factories;

use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'institution_id' => Institution::factory(),
            'name' => $this->faker->bs(),
            'city' => $this->faker->city(),
            'address' => $this->faker->address(),
        ];
    }
}
