<?php

namespace App\Services;

use App\Models\Assignment;

class AssignmentService
{
    public function create(array $data): Assignment
    {
        return Assignment::create($data);
    }

    public function assignProfessor($user, $offering, $disciplineId = null)
    {
        return Assignment::create([
            'user_id' => $user->id,
            'class_offering_id' => $offering->id,
            'project_id' => $offering->project_id,
            'course_id' => $offering->course_id,
            'unit_id' => $offering->unit_id,
            'assignment_type' => 'professor',
            'active' => true,
        ]);
    }

    public function assignSupervisor($user, $course)
    {
        return Assignment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'project_id' => $course->project_id,
            'unit_id' => $course->unit_id,
            'assignment_type' => 'supervisor',
            'active' => true,
        ]);
    }
}
