<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Institution;
use App\Models\ScholarshipHolder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class UserSeeder extends Seeder
{
    protected static ?string $password;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $password = static::$password ??= Hash::make('password');
        // Admin (vê tudo)
        $admin = User::firstOrCreate(
            ['email' => 'admin@bolsista.com'],
            [
                'name' => 'Super Admin',
                'email_verified_at' => now(),
                'password' => static::$password ??= Hash::make('password'),
                'remember_token' => Str::random(10),
            ]
        );

        $admin->syncRoles(['superadmin']);

        $institutions = Institution::with('units')->get();

        foreach ($institutions as $inst) {

            // Cria um usuário para a coordenação geral
            $coordenadorGeral = User::firstOrCreate(
                ['email' => "cg@{$inst->acronym}.example.com"],
                [
                    'name' => 'Coordenador Geral',
                    'unit_id' =>  null,
                    'institution_id' => $inst->id,
                    'email_verified_at' => now(),
                    'password' => $password,
                    'remember_token' => Str::random(10),
                ]
            );
            $coordenadorGeral->syncRoles(['coordenador_geral']);
            $this->ensureScholarshipHolder($coordenadorGeral);

            // Cria um usuário para a coordenação adjunta geral
            $coordenadorAdjuntoGeral = User::firstOrCreate(
                ['email' => "cag@{$inst->acronym}.example.com"],
                [
                    'name' => 'Coordenador Adjunto Geral',
                    'unit_id' =>  null,
                    'institution_id' => $inst->id,
                    'email_verified_at' => now(),
                    'password' => $password,
                    'remember_token' => Str::random(10),
                ]
            );
            $coordenadorAdjuntoGeral->syncRoles(['coordenador_adjunto_geral']);
            $this->ensureScholarshipHolder($coordenadorAdjuntoGeral);

            foreach ($inst->units as $unit) {
                
                // Cria um usuário para o coordenador adjunto
                $coordenadorAdjunto = User::firstOrCreate(
                    ['email' => "ca@{$unit->shortname}.example.com"],
                    [
                        'name' => "Coordenador Adjunto - {$unit->shortname}",
                        'unit_id' => $unit->id,
                        'institution_id' => $inst->id,
                        'email_verified_at' => now(),
                        'password' => $password,
                        'remember_token' => Str::random(10),
                    ]
                );
                $coordenadorAdjunto->syncRoles(['coordenador_adjunto']);
                $this->ensureScholarshipHolder($coordenadorAdjunto);

                for ($i = 1; $i <= 2; $i++) {
                    
                    // Cria usuários para os supervisores
                    $supervisor = User::firstOrCreate(
                        ['email' => "sup_{$i}@{$unit->shortname}.example.com"],
                        [
                            'name' => "Supervisor {$i} - {$unit->shortname}",
                            'unit_id' => $unit->id,
                            'institution_id' => $inst->id,
                        'email_verified_at' => now(),
                        'password' => $password,
                        'remember_token' => Str::random(10),
                    ]);
                    $supervisor->syncRoles(['supervisor']);
                    $this->ensureScholarshipHolder($supervisor);
                }
            }
        }

        $docentes =User::firstOrCreate(
            ['email' => "professor@{$inst->acronym}.example.com"],
            [
                'name' => 'Professor Exemplo',
                'unit_id' => null,
                'institution_id' => $inst->id,
                'email_verified_at' => now(),
                'password' => $password,
                'remember_token' => Str::random(10),
            ]
        );
        
        $docentes->syncRoles(['professor']);

        $this->command->info('Usuários padrão criados com sucesso.');
    }

    protected function ensureScholarshipHolder(User $user): void
    {
        ScholarshipHolder::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name'           => $user->name,
                'cpf'            => $this->randomCpf(),
                'unit_id'        => $user->unit_id ?? null,
                'status'         => 'active',
                'bank'     => Crypt::encryptString($this->randomBank()),
                'agency'   => Crypt::encryptString($this->randomAgency()),
                'account'  => Crypt::encryptString($this->randomAccount()),
                'pix_key'  => Crypt::encryptString($this->randomPixKey($user)),
            ]
        );
    }

    private function randomCpf(): string
    {
        $n = [];

        // Gera os 9 primeiros dígitos
        for ($i = 0; $i < 9; $i++) {
            $n[$i] = rand(0, 9);
        }

        // Calcula o primeiro dígito verificador
        $sum = 0;
        for ($i = 0, $weight = 10; $i < 9; $i++, $weight--) {
            $sum += $n[$i] * $weight;
        }
        $n[9] = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);

        // Calcula o segundo dígito verificador
        $sum = 0;
        for ($i = 0, $weight = 11; $i < 10; $i++, $weight--) {
            $sum += $n[$i] * $weight;
        }
        $n[10] = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);

        return sprintf(
            '%d%d%d.%d%d%d.%d%d%d-%d%d',
            ...$n
        );
    }

    private function randomBank(): string
    {
        return collect([
            'Banco do Brasil',
            'Caixa Econômica Federal',
            'Bradesco',
            'Itaú',
            'Santander',
            'Nubank',
        ])->random();
    }

    private function randomAgency(): string
    {
        return str_pad((string) rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    }

    private function randomAccount(): string
    {
        return rand(100000, 999999) . '-' . rand(0, 9);
    }

    private function randomPixKey(User $user): string
    {
        // usa email como chave PIX (bem realista)
        return $user->email;
    }

}