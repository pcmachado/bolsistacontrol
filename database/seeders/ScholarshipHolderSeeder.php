<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScholarshipHolder;
use App\Models\User;
use App\Models\Unit;
use Faker\Factory as Faker;

class ScholarshipHolderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $users = User::all();
        $units = Unit::all();

        foreach ($users as $user) {
            ScholarshipHolder::create([
                'name' => $user->name,
                'cpf' => $faker->unique()->cpf(false),
                'email' => $user->email,
                'phone' => $faker->phoneNumber(),
                'bank' => $faker->company(),
                'agency' => $faker->numerify('####'),
                'account' => $faker->numerify('######'),
                'user_id' => $user->id,
                'unit_id' => $units->random()->id,
                'start_date' => $faker->dateTimeBetween('-1 year', 'now'),
                'end_date' => $faker->dateTimeBetween('now', '+1 year'),
            ]);
        }
    }
}
