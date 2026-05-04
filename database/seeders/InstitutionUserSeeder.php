<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Seeder;

class InstitutionUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin → vinculado a todas as instituições
        $admin = User::whereIn('email', ['admin@example.com', 'admin@bolsista.com'])->first();
        if ($admin) {
            $institutions = Institution::all();
            foreach ($institutions as $institution) {
                $admin->institutions()->syncWithoutDetaching([
                    $institution->id => ['active' => true],
                ]);
            }
        }

        // Para cada usuário com institution_id já populado, vincula à instituição correspondente
        $users = User::whereNotNull('institution_id')->get();
        foreach ($users as $user) {
            $user->institutions()->syncWithoutDetaching([
                $user->institution_id => ['active' => true],
            ]);
        }

        // Para cada usuário com unit_id setado, vincula à instituição correspondente
        $users = User::whereNotNull('unit_id')->get();
        foreach ($users as $user) {
            $unit = $user->unit;
            if ($unit && $unit->institution_id) {
                $user->institutions()->syncWithoutDetaching([
                    $unit->institution_id => ['active' => true],
                ]);
            }
        }
    }
}
