<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Institution;

class InstitutionUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin → vinculado a todas as instituições
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $institutions = Institution::all();
            foreach ($institutions as $institution) {
                $admin->institutions()->syncWithoutDetaching([
                    $institution->id => ['active' => true],
                ]);
            }
        }

        // Coordenador Geral → vinculado à primeira instituição
        $coordenador = User::where('email', 'coordenador@example.com')->first();
        if ($coordenador) {
            $institution = Institution::first();
            if ($institution) {
                $coordenador->institutions()->syncWithoutDetaching([
                    $institution->id => ['active' => true],
                ]);
            }
        }

        // Coordenador Adjunto → vinculado à mesma instituição
        $adjunto = User::where('email', 'adjunto@example.com')->first();
        if ($adjunto && $institution) {
            $adjunto->institutions()->syncWithoutDetaching([
                $institution->id => ['active' => true],
            ]);
        }

        // Bolsista → vinculado à mesma instituição
        $bolsista = User::where('email', 'bolsista@example.com')->first();
        if ($bolsista && $institution) {
            $bolsista->institutions()->syncWithoutDetaching([
                $institution->id => ['active' => true],
            ]);
        }
    }
}
