<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['name' => 'Coordenador Geral', 'is_teacher' => false],
            ['name' => 'Coordenador Adjunto Geral', 'is_teacher' => false],
            ['name' => 'Coordenador Adjunto', 'is_teacher' => false],
            ['name' => 'Bolsista', 'is_teacher' => false],
            ['name' => 'Supervisor', 'is_teacher' => false],
            ['name' => 'Apoio Administrativo', 'is_teacher' => false],
            ['name' => 'Docente', 'is_teacher' => true], // 🔥 chave
        ];

        foreach ($positions as $p) {
            Position::updateOrCreate(
                ['name' => $p['name']],
                [
                    'description' => 'Descrição do cargo ' . $p['name'],
                    'is_teacher' => $p['is_teacher'],
                ]
            );
        }
    }
}
