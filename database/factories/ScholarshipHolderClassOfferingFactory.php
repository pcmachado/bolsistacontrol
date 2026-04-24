<?php

namespace Database\Factories;

use App\Models\ClassOffering;
use App\Models\ScholarshipHolder;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScholarshipHolderClassOfferingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'scholarship_holder_id' => ScholarshipHolder::inRandomOrder()->first()->id ?? ScholarshipHolder::factory(),
            'class_offering_id' => ClassOffering::inRandomOrder()->first()->id ?? ClassOffering::factory(),
            'role' => fake()->randomElement(['Orientador', 'Professor']),
        ];
    }
}