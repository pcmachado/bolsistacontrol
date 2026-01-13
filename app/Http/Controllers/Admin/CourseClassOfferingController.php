<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ClassOffering;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseClassOfferingController extends Controller
{
    public function index(Course $course)
    {
        $classOfferings = ClassOffering::where('course_id', $course->id)
            ->orderByDesc('created_at')
            ->get();

        return view(
            'admin.courses.class-offerings.index',
            compact('course', 'classOfferings')
        );
    }

    public function create(Course $course)
    {
        return view(
            'admin.courses.class-offerings.create',
            compact('course')
        );
    }
}
