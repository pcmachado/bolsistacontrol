<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Institution;
use Faker\Factory as Faker;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $institutions = Institution::all();

        foreach ($institutions as $institution) {
            for ($i = 0; $i < 3; $i++) {
                Project::create([
                    'name' => $faker->catchPhrase(),
                    'description' => $faker->paragraph(),
                    'institution_id' => $institution->id,
                    'start_date' => $faker->dateTimeBetween('-1 year', 'now'),
                    'end_date' => $faker->dateTimeBetween('now', '+1 year'),
                ]);
            }
        }
    }
}
