<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Instituition;
use Faker\Factory as Faker;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $instituitions = Instituition::all();

        foreach ($instituitions as $instituition) {
            for ($i = 0; $i < 3; $i++) {
                Project::create([
                    'name' => $faker->word(),
                    'description' => $faker->paragraph(),
                    'instituition_id' => $instituition->id,
                    'start_date' => $faker->dateTimeBetween('-1 year', 'now'),
                    'end_date' => $faker->dateTimeBetween('now', '+1 year'),
                ]);
            }
        }
    }
}
