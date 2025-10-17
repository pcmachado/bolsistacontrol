<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\institution;
use Faker\Factory as Faker;

class institutionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        for ($i = 0; $i < 5; $i++) {
            institution::create([
                'name' => $faker->company(),
                'city' => $faker->city(),
                'state' => $faker->stateAbbr(),
                'address' => $faker->address(),
                'phone' => $faker->phoneNumber(),
                'website' => $faker->url(),
                'cnpj' => $faker->cnpj(),
                'email' => $faker->unique()->companyEmail(),
                'acronym' => strtoupper($faker->lexify('???')),
                'contact_person' => $faker->name(),
                'contact_email' => $faker->unique()->companyEmail(),
                'contact_phone' => $faker->phoneNumber(),
                'logo_path' => null,
                'postal_code' => $faker->postcode(),
                'neighborhood' => $faker->word(),
                'complement' => $faker->secondaryAddress(),
                'number' => $faker->buildingNumber(),
                'country' => 'Brasil',
            ]);
        }
    }
}