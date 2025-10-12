<?php

namespace database\seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\Position;
use App\Models\ProjectScholarshipHolder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProjectScholarshipHolderSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::first();
        $holders = ScholarshipHolder::take(3)->get();
        $positions = Position::take(3)->get();

        foreach ($holders as $i => $holder) {
            ProjectScholarshipHolder::create([
                'project_id' => $project->id,
                'scholarship_holder_id' => $holder->id,
                'position_id' => $positions[$i % $positions->count()]->id,
                'weekly_hour_limit' => rand(10, 20),
                'start_date' => now()->subMonths(rand(1, 6)),
                'end_date' => null,
                'status' => 'active',
            ]);
        }
    }
}
