<?php

namespace database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InstituitionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}
