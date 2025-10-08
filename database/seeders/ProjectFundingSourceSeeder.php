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
       $projects = Project::all();
        $sources = FundingSource::all();

        foreach ($projects as $project) {
            $project->fundingSources()->attach(
                $sources->random(rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}
