<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseProject;

class CourseProjectSeeder extends Seeder
{
    public function run(): void
    {
        CourseProject::factory()->count(15)->create();
    }
}
