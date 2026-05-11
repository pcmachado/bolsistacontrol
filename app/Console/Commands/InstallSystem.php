<?php

namespace App\Console\Commands;

use App\Models\Institution;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InstallSystem extends Command
{
    protected $signature = 'system:install';

    protected $description = 'Instalação inicial do sistema';

    public function handle(): int
    {
        $this->info('');
        $this->info('===================================');
        $this->info(' INSTALAÇÃO INICIAL DO PROBOLSAS ');
        $this->info('===================================');
        $this->info('');

        // evitar reinstalação
        if (User::whereHas('roles', fn ($q) =>
            $q->where('name', 'superadmin')
        )->exists()) {

            $this->error('Já existe um superadmin cadastrado.');
            return self::FAILURE;
        }

        /*
        |--------------------------------------------------------------------------
        | INSTITUIÇÃO
        |--------------------------------------------------------------------------
        */
        $institutionName = $this->ask('Nome da instituição', 'Instituto Federal');
        $institutionShort = $this->ask('Sigla da instituição', 'IFRS');
        $institutionCity = $this->ask('Cidade da instituição', 'Bento Gonçalves');
        $institutionState = $this->ask('UF da instituição', 'RS');

        $institution = Institution::create([
            'name' => $institutionName,
            'shortname' => $institutionShort,
            'city' => $institutionCity,
            'state' => $institutionState,
        ]);

        /*
        |--------------------------------------------------------------------------
        | UNIDADE PRINCIPAL
        |--------------------------------------------------------------------------
        */
        $unitName = $this->ask('Nome da unidade principal', 'Reitoria');
        $unitCity = $this->ask('Cidade da unidade principal', 'Bento Gonçalves');

        $isAdministrativeAnswer = strtolower(
            $this->ask('A unidade principal é administrativa? (Sim/Não)', 'Sim')
        );

        $isAdministrative = in_array($isAdministrativeAnswer, ['sim', 's', 'yes', 'y']);

        $unitShort = $this->ask('Sigla da unidade principal', 'REI');

        $unit = Unit::create([
            'institution_id' => $institution->id,
            'name' => $unitName,
            'shortname' => $unitShort,
            'city' => $unitCity,
            'is_administrative' => $isAdministrative,
        ]);

        /*
        |--------------------------------------------------------------------------
        | SUPERADMIN
        |--------------------------------------------------------------------------
        */
        $name = $this->ask('Nome do superadmin', 'Super Admin');

        $email = $this->ask('E-mail do superadmin', 'pcmachado@live.com');

        // validar email único
        while (User::where('email', $email)->exists()) {
            $this->error('Este e-mail já está cadastrado.');
            $email = $this->ask('Informe outro e-mail');
        }

        $password = $this->secret('Senha');
        $confirm = $this->secret('Confirmar senha');

        if ($password !== $confirm) {
            $this->error('As senhas não conferem.');
            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'unit_id' => $unit->id,
            'email_verified_at' => now(),
        ]);

        $role = Role::where('name', 'superadmin')->first();

        if ($role) {
            $user->assignRole($role);
        }

        /*
        |--------------------------------------------------------------------------
        | FINALIZAÇÃO
        |--------------------------------------------------------------------------
        */
        $this->info('');
        $this->info('✅ Sistema instalado com sucesso!');
        $this->info('');
        $this->line("Instituição: {$institution->name}");
        $this->line("Unidade principal: {$unit->name}");
        $this->line("Usuário superadmin: {$email}");
        $this->info('');

        return self::SUCCESS;
    }
}
