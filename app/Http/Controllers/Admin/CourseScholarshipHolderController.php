<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseScholarshipHolder;
use App\Models\ScholarshipHolder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseScholarshipHolderController extends Controller
{
    public function index(Course $course)
    {
        $holders = $course->scholarshipHolders()->with('unit')->get();

        $available = ScholarshipHolder::all();

        return view('admin.courses.holders', compact('course', 'holders', 'available'));
    }

    public function create(Course $course): View
    {
        $availableScholarshipHolders = ScholarshipHolder::whereDoesntHave('courses', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })->orderBy('name')->get();

        return view(
            'admin.courses.scholarship-holders.create',
            compact('course', 'availableScholarshipHolders')
        );
    }

    public function store(Request $request, Course $course)
    {
        $request->validate([
            'scholarship_holder_id' => 'required|exists:scholarship_holders,id',
            'role' => 'required|in:orientador,supervisor',
        ]);

        $course->scholarshipHolders()->syncWithoutDetaching([
            $request->scholarship_holder_id => [
                'role' => $request->role
            ]
        ]);

        return back()->with('success', 'Vinculado com sucesso');
    }

    public function update(Request $request, Course $course, $holderId)
    {
        $course->scholarshipHolders()->updateExistingPivot($holderId, [
            'role' => $request->role
        ]);

        return back()->with('success', 'Atualizado');
    }

    public function destroy(Course $course, $holderId)
    {
        $course->scholarshipHolders()->detach($holderId);

        return back()->with('success', 'Desvinculado');
    }
}