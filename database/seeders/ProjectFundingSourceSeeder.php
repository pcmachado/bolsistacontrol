<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProjectFundingSource;
use App\Models\Project;
use App\Models\FundingSource;

class ProjectFundingSourceSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::first();
        $sources = FundingSource::take(2)->get();

        foreach ($sources as $source) {
            ProjectFundingSource::create([
                'project_id' => $project->id,
                'funding_source_id' => $source->id,
                'amount' => rand(5000, 20000),
                'start_date' => now()->subMonths(rand(1, 12)),
                'end_date' => null,
                'status' => 'active',
            ]);
        }
    }
}
