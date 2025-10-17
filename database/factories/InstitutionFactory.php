<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\institution;

class institutionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'website' => $this->faker->url(),
            'cnpj' => $this->faker->cnpj(),
            'acronym' => strtoupper($this->faker->lexify('???')),
            'email' => $this->faker->unique()->companyEmail(),
            'contact_person' => $this->faker->name(),
            'contact_email' => $this->faker->unique()->companyEmail(),
            'contact_phone' => $this->faker->phoneNumber(),
            'logo_path' => null,
            'postal_code' => $this->faker->postcode(),
            'neighborhood' => $this->faker->word(),
            'complement' => $this->faker->secondaryAddress(),
            'number' => $this->faker->buildingNumber(),
            'country' => 'Brasil',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
