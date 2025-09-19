<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institution;
use Faker\Factory as Faker;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        for ($i = 0; $i < 5; $i++) {
            Institution::create([
                'name' => $faker->company(),
                'city' => $faker->city(),
                'state' => $faker->stateAbbr(),
                'address' => $faker->address(),
                'phone' => $faker->phoneNumber(),
            ]);
        }
    }
}
