<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FundingSource;

class FundingSourceSeeder extends Seeder
{
    public function run(): void
    {
        FundingSource::factory()->count(10)->create();
    }
}
