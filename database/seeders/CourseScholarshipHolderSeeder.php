<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseScholarshipHolder;
use App\Models\ScholarshipHolder;

class CourseScholarshipHolderSeeder extends Seeder
{
    public function run(): void
    {
        //CourseScholarshipHolder::factory()->count(15)->create();

        $holders = ScholarshipHolder::pluck('id');

        if ($holders->isEmpty()) {
            echo "Nenhum bolsista encontrado — CourseScholarshipHolderSeeder ignorado.\n";
            return;
        }

        foreach ($holders as $holderId) {
            CourseScholarshipHolder::create([
                'course_id' => rand(1, 4),
                'scholarship_holder_id' => $holderId,
            ]);
        }
    }
}
