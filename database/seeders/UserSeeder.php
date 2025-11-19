<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Institution;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin (vê tudo)
        $admin = User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@bolsista.com',
        ]);
        $admin->assignRole('admin');

        $user = User::factory()->create([
                    'name' => 'Admin User',
                    'email' => 'admin@example.com',
                ]);
                $user->assignRole('Admin');

        $institutions = Institution::with('units')->get();

        foreach ($institutions as $inst) {
            // Cria um usuário para a coordenação geral
            $coordenadorGeral = User::factory()->create([
                'name' => 'Coordenador Geral',
                'email' => 'coordenador@example.com',
                'unit_id' =>  null,
            ]);
            $coordenadorGeral->assignRole('coordenador_geral');

            foreach ($inst->units as $unit) {

                // Cria um usuário para o coordenador adjunto
                $coordenadorAdjunto = User::factory()->create([
                    'name' => 'Coordenador Adjunto IFRS',
                    'email' => 'adjunto.ifrs@example.com',
                    'unit_id' => $unit->id,
                ]);
                $coordenadorAdjunto->assignRole('coordenador_adjunto');

                for ($i = 1; $i <= 2; $i++) {
                    // Cria usuários para os supervisores
                    $supervisor = User::factory()->create([
                        'name' => "Supervisor {$i} - {$unit->name}",
                        'email' => "supervisor{$i}." . strtolower(str_replace(' ', '_', $unit->name)) . "@example.com",
                        'unit_id' => $unit->id,
                    ]);
                    $supervisor->assignRole('supervisor');

                    // Cria um usuário de teste e atribui o papel de bolsista.
                    $bolsista = User::factory()->create([
                        'name' => "Bolsista {$unit->name}",
                        'email' => "bolsista." . strtolower(str_replace(' ', '_', $unit->name)) . "@example.com",
                        'unit_id' => $unit->id,
                    ]);
                    $bolsista->assignRole('bolsista');
                }
            }
        }
    }
}