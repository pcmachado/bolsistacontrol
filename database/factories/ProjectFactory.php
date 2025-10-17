<?php

namespace Database\Factories;

use App\Models\institution;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['MM', 'Extensão', 'Monitoria', 'Pesquisa', 'Inovação']),
            'description' => fake()->paragraph(2),
            'institution_id' => institution::inRandomOrder()->first()->id ?? institution::factory(),
            'start_date' => fake()->dateTimeBetween('-2 years', '-1 month'),
            'end_date' => fake()->dateTimeBetween('now', '+1 year'),
        ];
    }
}
