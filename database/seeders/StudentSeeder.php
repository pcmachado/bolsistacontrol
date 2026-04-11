<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\ClassOffering;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $classes = ClassOffering::all();

        foreach ($classes as $class) {

            for ($i = 1; $i <= rand(15, 30); $i++) {

                Student::create([
                    'class_offering_id' => $class->id,
                    'name' => fake()->name(),
                    'cpf' => fake()->numerify('###########'),
                    'payment_type' => fake()->randomElement(['pix','transfer']),
                    'pix_key' => fake()->email(),
                ]);
            }
        }
    }
}
