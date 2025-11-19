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
        $admin = User::whereIn('email', ['admin@example.com', 'admin@bolsista.com'])->first();
        if ($admin) {
            $institutions = Institution::all();
            foreach ($institutions as $institution) {
                $admin->institutions()->syncWithoutDetaching([
                    $institution->id => ['active' => true],
                ]);
            }
        }

        // Para cada usuário com unit_id setado, vincula à instituição correspondente
        $users = User::whereNotNull('unit_id')->get();
        foreach ($users as $user) {
            $unit = $user->unit;
            if ($unit && $unit->institution_id) {
                $user->institutions()->syncWithoutDetaching($unit->institution_id);
            }
        }

        // Coordenadores gerais: vincula o CG à sua instituição
        $cgs = User::role('coordenador_geral')->get();
        foreach ($cgs as $cg) {
            // tenta inferir instituição via unidade dos adjuntos ou pelo nome
            // fallback: vincula à primeira instituição (defensivo)
            $inst = Institution::where('name', 'like', "%{$cg->name}%")->first() ?? Institution::first();
            if ($inst) $cg->institutions()->syncWithoutDetaching($inst->id);
        }

        /*
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
        }*/
    }
}
