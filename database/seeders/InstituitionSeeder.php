<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Instituition;
use Faker\Factory as Faker;

class InstituitionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        for ($i = 0; $i < 5; $i++) {
            Instituition::create([
                'name' => $faker->company(),
                'city' => $faker->city(),
                'state' => $faker->stateAbbr(),
                'address' => $faker->address(),
                'phone' => $faker->phoneNumber(),
            ]);
        }
    }
}
