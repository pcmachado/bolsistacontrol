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
        /*$project = Project::first();
        $holders = ScholarshipHolder::take(3)->get();
        $positions = Position::take(3)->get();

        foreach ($holders as $i => $holder) {
            ProjectScholarshipHolder::create([
                'project_id' => $project->id,
                'scholarship_holder_id' => $holder->id,
                'position_id' => $positions[$i % $positions->count()]->id,
                'weekly_workload' => rand(10, 20),
                'start_date' => now()->subMonths(rand(1, 6)),
                'end_date' => null,
                'status' => 'active',
            ]);
        }*/

            $projects = Project::with('instituition')->get();

        foreach ($projects as $project) {
            $instId = $project->instituition_id ?? $project->instituition?->id;

            // pega bolsistas da mesma instituição via unit->institution
            $holders = ScholarshipHolder::whereHas('unit', function ($q) use ($instId) {
                $q->where('institution_id', $instId);
            })->get();

            // anexa até 6 bolsistas por projeto (ou menos se não houver)
            $toAttach = $holders->take(6);

            foreach ($toAttach as $holder) {
                // Se existir pivot model, use create; senão attach via DB
                try {
                    \DB::table('project_scholarship_holders')->insert([
                        'project_id' => $project->id,
                        'scholarship_holder_id' => $holder->id,
                        'position_id' => null,
                        'weekly_workload' => rand(10, 20),
                        'status' => 'active',
                        'start_date' => now()->toDateString(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    // ignore duplicates
                }
            }
        }
    }
}
