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
                [
                    'name' => 'UCS - Tecnologia',
                    'shortname' => 'tecnologia',
                    'domain' => 'tecnologia.ucs.br'
                ],
                [
                    'name' => 'UCS - Bento Gonçalves',
                    'shortname' => 'bento',
                    'domain' => 'bento.ucs.br'
                ],
                [
                    'name' => 'UCS - Caxias do Sul',
                    'shortname' => 'caxias',
                    'domain' => 'ucs.br'
                ]
            ],
            'Instituto Federal do Rio Grande do Sul' => [
                [
                    'name' => 'IFRS - Campus Bento Gonçalves',
                    'shortname' => 'bento',
                    'domain' => 'bento.ifrs.edu.br'
                ],
                [
                    'name' => 'IFRS - Campus Farroupilha',
                    'shortname' => 'farroupilha',
                    'domain' => 'farroupilha.ifrs.edu.br'
                ],
                [
                    'name' => 'IFRS - Reitoria',
                    'shortname' => 'reitoria',
                    'domain' => 'ifrs.edu.br'
                ]
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
                        'name' => $u['name'],
                        'domain' => $u['domain'],
                        'shortname' => $u['shortname'],
                    ],
                    [
                        'city' => $faker->city(),
                        'address' => $faker->address(),
                        'phone' => $faker->phoneNumber(),
                        'email' => $faker->unique()->companyEmail(),
                        'cnpj' => $faker->cnpj(),
                    ]
            );
                echo "   - Unidade criada: {$u['name']}\n";
            }
        }
    }
}
