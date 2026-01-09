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
        ProjectFundingSource::insert([
            [
                'project_id' => 1,
                'funding_source_id' => 2,
                'allocated_amount' => rand(5000, 20000),
                'used_amount' => rand(1000, 5000),
                'start_date' => now()->subMonths(rand(1, 12)),
                'end_date' => null,
                'status' => 'active',
            ],
            [
                'project_id' => 2,
                'funding_source_id' => 2,
                'allocated_amount' => rand(5000, 20000),
                'used_amount' => rand(1000, 5000),
                'start_date' => now()->subMonths(rand(1, 12)),
                'end_date' => null,
                'status' => 'active',
            ],
            [
                'project_id' => 3,
                'funding_source_id' => 1,
                'allocated_amount' => rand(5000, 20000),
                'used_amount' => rand(1000, 5000),
                'start_date' => now()->subMonths(rand(1, 12)),
                'end_date' => null,
                'status' => 'active',
            ],
            [
                'project_id' => 4,
                'funding_source_id' => 2,
                'allocated_amount' => rand(5000, 20000),
                'used_amount' => rand(1000, 5000),
                'start_date' => now()->subMonths(rand(1, 12)),
                'end_date' => null,
                'status' => 'active',
            ],
        ]);
    }
}
