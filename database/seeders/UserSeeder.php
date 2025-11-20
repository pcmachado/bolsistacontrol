<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    protected static ?string $password;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin (vê tudo)
        $admin = User::firstOrCreate([
            'name' => 'Administrador',
            'email' => 'admin@bolsista.com',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $admin->assignRole('admin');

        $user = User::firstOrCreate([
                    'name' => 'Admin User',
                    'email' => 'admin@example.com',
                    'email_verified_at' => now(),
                    'password' => static::$password ??= Hash::make('password'),
                    'remember_token' => Str::random(10),
                ]);
                $user->assignRole('Admin');

        $institutions = Institution::with('units')->get();

        foreach ($institutions as $inst) {
            $cgEmail = "cg_{$inst->id}@example.com";
            // Cria um usuário para a coordenação geral
            $coordenadorGeral = User::firstOrCreate(
                ['email' => $cgEmail],
                [
                    'name' => 'Coordenador Geral',
                    'unit_id' =>  null,
                    'email_verified_at' => now(),
                    'password' => static::$password ??= Hash::make('password'),
                    'remember_token' => Str::random(10),
                ]
        );
            $coordenadorGeral->assignRole('coordenador_geral');

            foreach ($inst->units as $unit) {
                $caEmail = "ca_unit_{$unit->id}@example.com";
                // Cria um usuário para o coordenador adjunto
                $coordenadorAdjunto = User::firstOrCreate(
                    ['email' => $caEmail],
                    [
                        'name' => 'Coordenador Adjunto IFRS',
                        'unit_id' => $unit->id,
                        'email_verified_at' => now(),
                        'password' => static::$password ??= Hash::make('password'),
                        'remember_token' => Str::random(10),
                    ]
                );
                $coordenadorAdjunto->assignRole('coordenador_adjunto');

                for ($i = 1; $i <= 2; $i++) {
                    $supervisorEmail = "sup{$i}_unit_{$unit->id}@example.com";
                    // Cria usuários para os supervisores
                    $supervisor = User::firstOrCreate(
                        ['email' => $supervisorEmail],
                        [
                            'name' => "Supervisor {$i} - {$unit->name}",
                            'unit_id' => $unit->id,
                        'email_verified_at' => now(),
                        'password' => static::$password ??= Hash::make('password'),
                        'remember_token' => Str::random(10),
                    ]);
                    $supervisor->assignRole('supervisor');

                    $bolsistaEmail = "bols{$i}_unit_{$unit->id}@example.com";
                    // Cria um usuário de teste e atribui o papel de bolsista.
                    $bolsista = User::firstOrCreate(
                        ['email' => $bolsistaEmail],
                        [
                            'name' => "Bolsista {$unit->name}",
                            'unit_id' => $unit->id,
                        'email_verified_at' => now(),
                        'password' => static::$password ??= Hash::make('password'),
                        'remember_token' => Str::random(10),
                    ]);
                    $bolsista->assignRole('bolsista');
                }
            }
        }
    }
}