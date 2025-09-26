<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cria um usuário de teste e atribui o papel de bolsista.
        $bolsista = User::factory()->create([
            'name' => 'Bolsista de Teste',
            'email' => 'bolsista@example.com'
        ]);
        $bolsista->assignRole('bolsista');

        // Cria um usuário para o coordenador adjunto
        $coordenadorAdjunto = User::factory()->create([
            'name' => 'Coordenador Adjunto',
            'email' => 'adjunto@example.com'
        ]);
        $coordenadorAdjunto->assignRole('coordenador_adjunto');

        // Cria um usuário para a coordenação geral
        $coordenadorGeral = User::factory()->create([
            'name' => 'Coordenador Geral',
            'email' => 'coordenador@example.com'
        ]);
        $coordenadorGeral->assignRole('coordenador_geral');
    }
}