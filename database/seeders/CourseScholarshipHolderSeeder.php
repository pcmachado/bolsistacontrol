<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseScholarshipHolder;

class CourseScholarshipHolderSeeder extends Seeder
{
    public function run(): void
    {
        //CourseScholarshipHolder::factory()->count(15)->create();

        for ($i = 1; $i <= 12; $i++) {
            CourseScholarshipHolder::create([
                'course_id' => rand(1, 4),
                'scholarship_holder_id' => $i,
            ]);
        }
    }
}
