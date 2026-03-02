<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\ScholarshipHolder;
use Illuminate\Database\Seeder;

class CourseScholarshipHolderSeeder extends Seeder
{
    public function run(): void
    {
        $holders = ScholarshipHolder::all();
        $courses = Course::all();

        if ($holders->isEmpty() || $courses->isEmpty()) {
            $this->command?->warn('CourseScholarshipHolderSeeder: bolsistas/cursos ausentes.');
            return;
        }

        foreach ($holders as $holder) {
            $sample = $courses->shuffle()->take(min(2, $courses->count()));
            $holder->courses()->syncWithoutDetaching($sample->pluck('id')->all());
        }
    }
}
