<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseScholarshipHolder;

class CourseScholarshipHolderSeeder extends Seeder
{
    public function run(): void
    {
        CourseScholarshipHolder::factory()->count(15)->create();
    }
}
