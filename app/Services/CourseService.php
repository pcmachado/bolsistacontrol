<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function createCourse(array $data): Course
    {
        return DB::transaction(function () use ($data) {

            $course = Course::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'duration_hours' => $data['duration_hours'] ?? null,
                'prerequisites' => $data['prerequisites'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'active' => $data['active'] ?? true,
                'institution_id' => $data['institution_id'] ?? null,
                'capacity' => $data['capacity'] ?? null,
            ]);

            $this->syncProjects($course, $data);

            return $course;
        });
    }

    public function updateCourse(Course $course, array $data): Course
    {
        return DB::transaction(function () use ($course, $data) {

            $course->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'duration_hours' => $data['duration_hours'] ?? null,
                'prerequisites' => $data['prerequisites'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'active' => $data['active'] ?? false,
                'institution_id' => $data['institution_id'] ?? null,
                'capacity' => $data['capacity'] ?? null,
            ]);

            $this->syncProjects($course, $data);

            return $course;
        });
    }

    public function deleteCourse(Course $course): bool
    {
        return $course->delete();
    }

    protected function syncProjects(Course $course, array $data): void
    {
        if (!empty($data['project_id'])) {

            $course->projects()->syncWithoutDetaching([
                $data['project_id'] => [
                    'active' => true,
                    'start_date' => $data['start_date'] ?? null,
                    'end_date' => $data['end_date'] ?? null,
                ],
            ]);
        }
    }
}
