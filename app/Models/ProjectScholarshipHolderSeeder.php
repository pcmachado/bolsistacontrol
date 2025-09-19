<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\Position;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProjectScholarshipHolderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $projects = Project::all();
        $scholars = ScholarshipHolder::all();
        $positions = Position::all();

        foreach ($scholars as $scholar) {
            $project = $projects->random();
            DB::table('project_scholarship_holders')->insert([
                'project_id' => $project->id,
                'scholarship_holder_id' => $scholar->id,
                'position_id' => $positions->random()->id,
                'monthly_workload' => $faker->randomFloat(1, 10, 40),
                'start_date' => $faker->dateTimeBetween('-6 months', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
