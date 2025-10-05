<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScholarshipHolderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'cpf' => $this->faker->unique()->cpf(false),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'bank' => $this->faker->company(),
            'agency' => $this->faker->numerify('####'),
            'account' => $this->faker->numerify('######'),
            'user_id' => User::factory(),
            'unit_id' => Unit::factory(),
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
        ];
    }
}
