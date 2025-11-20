<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Institution;
use Faker\Factory as Faker;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        /*$institutions = Institution::all();

        foreach ($institutions as $institution) {
            for ($i = 0; $i < 2; $i++) {
                Unit::create([
                    'institution_id' => $institution->id,
                    'name' => $faker->word(),
                    'city' => $faker->city(),
                    'address' => $faker->address(),
                ]);
            }
        }*/

        $map = [
            'Universidade de Caxias do Sul' => [
                'UCS - Tecnologia', 'UCS - Saúde', 'UCS - Engenharias'
            ],
            'Instituto Federal do Rio Grande do Sul' => [
                'IFRS - Campus Bento Gonçalves', 'IFRS - Campus Farroupilha', 'IFRS - Reitoria'
            ],
        ];

        foreach ($map as $instName => $units) {
            $inst = Institution::where('name', $instName)->first();

            if (!$inst) {
                echo "⚠ Instituição não encontrada: {$instName}\n";
                continue;
            }

            echo "✔ Criando unidades para: {$instName} (ID: {$inst->id})\n";

            foreach ($units as $u) {
                Unit::firstOrCreate(
                    [
                        'institution_id' => $inst->id,
                        'name' => $u,
                    ],
                    [
                        'city' => $faker->city(),
                        'address' => $faker->address(),
                    ]
            );
                echo "   - Unidade criada: {$u}\n";
            }
        }
    }
}
