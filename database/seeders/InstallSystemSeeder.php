<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InstallSystemSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | INSTITUIÇÃO
        |--------------------------------------------------------------------------
        */
        $institution = Institution::create([
            'name'       => 'Instituto Federal de Educação, Ciência e Tecnologia do Rio Grande do Sul',
            'shortname'  => 'IFRS',
            'city'       => 'Bento Gonçalves',
            'state'      => 'RS',
        ]);

        /*
        |--------------------------------------------------------------------------
        | UNIDADE PRINCIPAL
        |--------------------------------------------------------------------------
        */
        $unit = Unit::create([
            'institution_id'   => $institution->id,
            'name'             => 'Reitoria',
            'shortname'        => 'REI',
            'city'             => 'Bento Gonçalves',
            'is_administrative'=> true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | SUPERADMIN
        |--------------------------------------------------------------------------
        */
        // garante que o papel exista
        $role = Role::firstOrCreate(['name' => 'superadmin']);

        $user = User::create([
            'name'              => 'Super Admin',
            'email'             => 'pcmachado@live.com',
            'password'          => Hash::make('admin123'),
            'unit_id'           => $unit->id,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($role);
    }
}
