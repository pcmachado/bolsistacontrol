<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProjectPosition;

class ProjectPositionSeeder extends Seeder
{
    public function run(): void
    {
        ProjectPosition::factory()->count(20)->create();
    }
}
