<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'cpf' => $this->faker->unique()->numerify('###########'),
            'passport' => $this->faker->optional()->regexify('[A-Z]{2}[0-9]{6}'),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'pix_key' => $this->faker->optional()->email(),
            'bank' => $this->faker->optional()->company(),
            'agency' => $this->faker->optional()->numerify('####'),
            'account' => $this->faker->optional()->numerify('#########'),
        ];
    }
}
