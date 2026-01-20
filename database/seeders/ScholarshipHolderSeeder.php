<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScholarshipHolder;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class ScholarshipHolderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        //$users = User::all();
        //$units = Unit::all();

        $units = Unit::all();

        foreach ($units as $unit) {
            for ($i = 1; $i <= 2; $i++) {
                $email = "bolsista{$i}@{$unit->shortname}.example.com";

                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => "Bolsista {$i} - {$unit->shortname}",
                        'institution_id' => $unit->institution_id,
                        'unit_id' => $unit->id,
                        'password' => Hash::make('password'),
                    ]
                );

                if (!$user->scholarshipHolder !== null) {
                    $user->assignRole('bolsista');
                }

                ScholarshipHolder::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                    'name' => $user->name,
                    'cpf' => substr(preg_replace('/\D/', '', (string)microtime()), 0, 11) . rand(10,99),
                    'email' => $user->email,
                    'phone' => $faker->phoneNumber(),
                    'bank' => $faker->company(),
                    'agency' => $faker->numerify('####'),
                    'account' => $faker->numerify('######'),
                    'pix_key' => $faker->unique()->email(),
                    'user_id' => $user->id,
                    'unit_id' => $unit->id,
                    'start_date' => $faker->dateTimeBetween('-1 year', 'now'),
                    'end_date' => $faker->dateTimeBetween('now', '+1 year'),
                    'status' => 'active',
                    ]
                );
            
            }
        }
    }
}
