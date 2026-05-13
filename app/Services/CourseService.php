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
        return Course::create($data);
    }
}
