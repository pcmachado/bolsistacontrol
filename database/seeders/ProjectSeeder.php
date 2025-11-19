<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\institution;
use Faker\Factory as Faker;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        /*$faker = Faker::create('pt_BR');
        $institutions = institution::all();

        foreach ($institutions as $institution) {
            for ($i = 0; $i < 3; $i++) {
                Project::create([
                    'name' => $faker->word(),
                    'description' => $faker->paragraph(),
                    'institution_id' => $institution->id,
                    'start_date' => $faker->dateTimeBetween('-1 year', 'now'),
                    'end_date' => $faker->dateTimeBetween('now', '+1 year'),
                ]);
            }
        }*/

        $institutions = Institution::all();

        foreach ($institutions as $inst) {
            for ($i = 1; $i <= 2; $i++) {
                Project::create([
                    'name' => "{$inst->name} - Projeto {$i}",
                    'description' => "Projeto {$i} da {$inst->name}",
                    'institution_id' => $inst->id, 
                    'start_date' => now()->subMonths(5)->toDateString(),
                    'end_date' => null,
                ]);
            }
        }
    }
}
