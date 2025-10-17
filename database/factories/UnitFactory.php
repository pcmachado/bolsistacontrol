<?php

namespace Database\Factories;

use App\Models\institution;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'institution_id' => institution::factory(),
            'name' => $this->faker->word(),
            'city' => $this->faker->city(),
            'address' => $this->faker->address(),
        ];
    }
}
