<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\institution;
use Faker\Factory as Faker;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        /*$faker = Faker::create('pt_BR');
        $institutions = institution::all();

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
            'Universidade de Caxias do Sul - UCS' => [
                'UCS - Tecnologia', 'UCS - Saúde', 'UCS - Engenharias'
            ],
            'Instituto Federal do Rio Grande do Sul - IFRS' => [
                'IFRS - Campus Bento Gonçalves', 'IFRS - Campus Farroupilha', 'IFRS - Reitoria'
            ],
        ];

        foreach ($map as $instName => $units) {
            $inst = Institution::where('name', $instName)->first();
            if (!$inst) continue;
            foreach ($units as $u) {
                Unit::create([
                    'institution_id' => $inst->id,
                    'name' => $u,
                    'city' => $inst->city ?? null,
                    'address' => $inst->address ?? null,
                ]);
            }
        }
    }
}
