<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Services\CourseService;
use App\DataTables\CoursesDataTable;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index(CoursesDataTable $dataTable)
    {
        return $dataTable->render('admin.courses.index');
    }

    public function create(): View
    {
        return view('admin.courses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
        ];

        $validated = $request->validate($rules);

        $this->courseService->createCourse($validated);

        return redirect()->route('admin.courses.index')->with('success', 'Curso cadastrado com sucesso!');
    }

    public function show(Course $course): View
    {
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $course->update($request->all());

        return redirect()->route('admin.courses.index')->with('success', 'Curso atualizado com sucesso!');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Curso excluído com sucesso!');
    }
}
