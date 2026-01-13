<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Position;

class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        // Lista fixa de posições
        $positions = [
            'Coordenador Geral',
            'Coordenador Adjunto',
            'Bolsista',
            'Supervisor',
            'Apoio Administrativo',
            'Docente',
        ];

        $name = $this->faker->unique()->randomElement($positions);

        return [
            'name' => $name,
            'description' => 'Descrição do cargo ' . $name,
        ];
    }
}
