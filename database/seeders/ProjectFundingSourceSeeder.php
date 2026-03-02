<?php

namespace Database\Seeders;

use App\Models\FundingSource;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectFundingSourceSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();
        $sources = FundingSource::all();

        if ($projects->isEmpty() || $sources->isEmpty()) {
            $this->command?->warn('ProjectFundingSourceSeeder: projetos/fontes ausentes.');
            return;
        }

        foreach ($projects as $project) {
            $source = $sources->random();

            $project->fundingSources()->syncWithoutDetaching([
                $source->id => [
                    'used_amount' => rand(5000, 25000),
                    'start_date' => $project->start_date,
                    'end_date' => $project->end_date,
                    'status' => 'active',
                ],
            ]);
        }
    }
}
