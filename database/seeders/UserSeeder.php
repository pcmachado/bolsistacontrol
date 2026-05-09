<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\ScholarshipHolder;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    protected static ?string $password = null;

    public function run(): void
    {
        $password = static::$password ??= Hash::make('password');

        $superadmin = User::updateOrCreate(
            ['email' => 'superadmin@bolsista.com'],
            [
                'name' => 'Super Admin',
                'email_verified_at' => now(),
                'password' => $password,
                'remember_token' => Str::random(10),
                'institution_id' => null,
                'unit_id' => null,
            ]
        );

        $superadmin->syncRoles(['superadmin']);

        $institutions = Institution::with('units')->orderBy('name')->get();

        foreach ($institutions as $institution) {
            $this->upsertUser(
                email: "admin@{$institution->acronym}.example.com",
                name: "Administrador {$institution->acronym}",
                role: 'admin',
                institutionId: $institution->id
            );

            $this->upsertUser(
                email: "cg@{$institution->acronym}.example.com",
                name: "Coordenador Geral {$institution->acronym}",
                role: 'coordenador_geral',
                institutionId: $institution->id
            );

            $this->upsertUser(
                email: "cag@{$institution->acronym}.example.com",
                name: "Coordenador Adjunto Geral {$institution->acronym}",
                role: 'coordenador_adjunto_geral',
                institutionId: $institution->id
            );

            for ($index = 1; $index <= 2; $index++) {
                $this->upsertUser(
                    email: "prof{$index}@{$institution->acronym}.example.com",
                    name: "Professor {$index} {$institution->acronym}",
                    role: 'professor',
                    institutionId: $institution->id
                );
            }

            foreach ($institution->units as $unit) {
                $this->upsertUser(
                    email: "ca@{$unit->shortname}.example.com",
                    name: "Coordenador Adjunto {$unit->shortname}",
                    role: 'coordenador_adjunto',
                    institutionId: $institution->id,
                    unitId: $unit->id
                );

                for ($index = 1; $index <= 2; $index++) {
                    $this->upsertUser(
                        email: "sup{$index}@{$unit->shortname}.example.com",
                        name: "Supervisor {$index} {$unit->shortname}",
                        role: 'supervisor',
                        institutionId: $institution->id,
                        unitId: $unit->id
                    );
                }
            }
        }

        $this->command?->info('Usuarios base criados com sucesso.');
    }

    protected function upsertUser(string $email, string $name, string $role, ?int $institutionId = null, ?int $unitId = null): User
    {
        if ($unitId === null && $institutionId !== null) {
            if (in_array($role, ['coordenador_adjunto_geral', 'coordenador_geral'])) {
                $unitId = Unit::where('institution_id', $institutionId)->where('shortname', 'like', '%reitoria%')->first()?->id;
            } else {
                $unitId = Unit::where('institution_id', $institutionId)->inRandomOrder()->first()?->id;
            }
        }

        $password = static::$password ??= Hash::make('password');

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'institution_id' => $institutionId,
                'unit_id' => $unitId,
                'email_verified_at' => now(),
                'password' => $password,
                'remember_token' => Str::random(10),
            ]
        );

        $user->syncRoles([$role]);

        if ($institutionId) {
            $user->institutions()->syncWithoutDetaching([
                $institutionId => ['active' => true],
            ]);
        }

        if (! in_array($role, ['superadmin', 'admin'])) {
            $this->ensureScholarshipHolder($user, $unitId);
        }

        return $user;
    }

    protected function ensureScholarshipHolder(User $user, ?int $unitId): void
    {
        $unit = $unitId ? Unit::find($unitId) : null;

        ScholarshipHolder::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'cpf' => $this->documentFromUser($user, 11),
                'email' => $user->email,
                'phone' => '(54) 99999-0000',
                'bank' => 'Banco do Brasil',
                'agency' => '1234',
                'account' => '567890-1',
                'pix_key' => $user->email,
                'unit_id' => $unit?->id,
                'status' => 'active',
                'start_date' => now()->subMonths(6)->toDateString(),
                'end_date' => null,
            ]
        );
    }

    protected function documentFromUser(User $user, int $length): string
    {
        return str_pad((string) $user->id, $length, '0', STR_PAD_LEFT);
    }
}
