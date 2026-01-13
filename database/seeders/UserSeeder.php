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
            'name' => 'Super Admin',
            'email' => 'admin@bolsista.com',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $admin->assignRole('superadmin');

        $institutions = Institution::with('units')->get();

        foreach ($institutions as $inst) {
            $cgEmail = "cg@{$inst->acronym}.example.com";
            // Cria um usuário para a coordenação geral
            $coordenadorGeral = User::firstOrCreate(
                ['email' => $cgEmail],
                [
                    'name' => 'Coordenador Geral',
                    'unit_id' =>  null,
                    'institution_id' => $inst->id,
                    'email_verified_at' => now(),
                    'password' => static::$password ??= Hash::make('password'),
                    'remember_token' => Str::random(10),
                ]
        );
            $coordenadorGeral->assignRole('coordenador_geral');

            $cagEmail = "cag@{$inst->acronym}.example.com";
            // Cria um usuário para a coordenação adjunta geral
            $coordenadorAdjuntoGeral = User::firstOrCreate(
                ['email' => $cagEmail],
                [
                    'name' => 'Coordenador Adjunto Geral',
                    'unit_id' =>  null,
                    'institution_id' => $inst->id,
                    'email_verified_at' => now(),
                    'password' => static::$password ??= Hash::make('password'),
                    'remember_token' => Str::random(10),
                ]
        );
            $coordenadorAdjuntoGeral->assignRole('coordenador_adjunto_geral');

            foreach ($inst->units as $unit) {
                $caEmail = "ca@{$unit->shortname}.example.com";
                // Cria um usuário para o coordenador adjunto
                $coordenadorAdjunto = User::firstOrCreate(
                    ['email' => $caEmail],
                    [
                        'name' => "Coordenador Adjunto - {$unit->shortname}",
                        'unit_id' => $unit->id,
                        'institution_id' => $inst->id,
                        'email_verified_at' => now(),
                        'password' => static::$password ??= Hash::make('password'),
                        'remember_token' => Str::random(10),
                    ]
                );
                $coordenadorAdjunto->assignRole('coordenador_adjunto');

                for ($i = 1; $i <= 2; $i++) {
                    $supervisorEmail = "sup_{$i}@{$unit->shortname}.example.com";
                    // Cria usuários para os supervisores
                    $supervisor = User::firstOrCreate(
                        ['email' => $supervisorEmail],
                        [
                            'name' => "Supervisor {$i} - {$unit->shortname}",
                            'unit_id' => $unit->id,
                            'institution_id' => $inst->id,
                        'email_verified_at' => now(),
                        'password' => static::$password ??= Hash::make('password'),
                        'remember_token' => Str::random(10),
                    ]);
                    $supervisor->assignRole('supervisor');
                }
            }
        }
    }
}