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
        $institutions = Institution::all();

        foreach ($institutions as $institution) {
            for ($i = 0; $i < 2; $i++) {
                Unit::create([
                    'institution_id' => $institution->id,
                    'name' => $faker->bs(),
                    'city' => $faker->city(),
                    'address' => $faker->address(),
                ]);
            }
        }
    }
}
