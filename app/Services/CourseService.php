<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseService
{
    /**
     * Cria um novo curso.
     *
     * @param array $data Dados do curso
     * @return Course
     */
    public function createCourse(array $data): Course
    {
        $course = Course::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'duration_hours' => $data['duration_hours'] ?? null,
            'prerequisites' => $data['prerequisites'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'active' => $data['active'] ?? true,
            'institution_id' => $data['institution_id'] ?? null,
        ]);

        if (! empty($data['project_id'])) {
            $course->projects()->syncWithoutDetaching([
                $data['project_id'] => [
                    'active' => true,
                    'start_date' => $data['start_date'] ?? null,
                    'end_date' => $data['end_date'] ?? null,
                ],
            ]);
        }

        return $course;
    }

    /**
     * Atualiza um curso existente.
     *
     * @param Course $course
     * @param array $data
     * @return Course
     */
    public function updateCourse(Course $course, array $data): Course
    {
        $course->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'duration_hours' => $data['duration_hours'] ?? null,
            'prerequisites' => $data['prerequisites'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'active' => $data['active'] ?? false,
            'institution_id' => $data['institution_id'] ?? null,
        ]);

        if (! empty($data['project_id'])) {
            $course->projects()->syncWithoutDetaching([
                $data['project_id'] => [
                    'active' => true,
                    'start_date' => $data['start_date'] ?? null,
                    'end_date' => $data['end_date'] ?? null,
                ],
            ]);
        }

        return $course;
    }
}
