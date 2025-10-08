<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = ['Coordenador Geral', 'Coordenador Adjunto', 'Bolsista', 'Supervisor', 'Apoio Administrativo', 'Docente'];

        foreach ($positions as $name) {
            Position::create(['name' => $name]);
        }
    }
}
