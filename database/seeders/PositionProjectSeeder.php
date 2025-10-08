<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PositionProject;

class PositionProjectSeeder extends Seeder
{
    public function run(): void
    {
        PositionProject::factory()->count(20)->create();
    }
}
