<?php

namespace Database\Factories;

use App\Models\Instituition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['MM', 'Extensão', 'Monitoria', 'Pesquisa', 'Inovação']),
            'description' => fake()->paragraph(2),
            'instituition_id' => Instituition::inRandomOrder()->first()->id ?? Instituition::factory(),
            'start_date' => fake()->dateTimeBetween('-2 years', '-1 month'),
            'end_date' => fake()->dateTimeBetween('now', '+1 year'),
        ];
    }
}
