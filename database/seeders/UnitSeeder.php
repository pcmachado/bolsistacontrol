<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Instituition;
use Faker\Factory as Faker;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $instituitions = Instituition::all();

        foreach ($instituitions as $instituition) {
            for ($i = 0; $i < 2; $i++) {
                Unit::create([
                    'instituition_id' => $instituition->id,
                    'name' => $faker->word(),
                    'city' => $faker->city(),
                    'address' => $faker->address(),
                ]);
            }
        }
    }
}
