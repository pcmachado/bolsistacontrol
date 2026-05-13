<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\CoursesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Institution;
use App\Models\Project;
use App\Models\Unit;
use App\Http\Requests\CourseRequest;
use App\Services\CourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseController extends Controller
{
    protected CourseService $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index(CoursesDataTable $dataTable)
    {
        $user = Auth::user();

        $projects = Project::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $user->visibleProjectIds())
            ->orderBy('name')
            ->get();

        $units = Unit::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $user->visibleUnitIds())
            ->orderBy('name')
            ->get();

        return $dataTable->render('admin.courses.index', compact('projects', 'units'));
    }

    public function create(): View
    {
        $user = Auth::user();

        $projects = Project::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $user->visibleProjectIds())
            ->orderBy('name')
            ->get();

        $institutions = Institution::orderBy('name')->get();

        return view('admin.courses.create', compact('projects', 'institutions'));
    }

    public function store(CourseRequest $request)
    {
        $course = $this->courseService->createCourse($request->validated());

        if ($request->ajax()) {
            return response()->json($course);
        }

        return redirect()->route('admin.courses.index')->with('success', 'Curso cadastrado com sucesso!');
    }

    public function show(Course $course): View
    {
        $course->load('projects', 'institution', 'disciplines');

        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        $user = Auth::user();

        $projects = Project::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $user->visibleProjectIds())
            ->orderBy('name')
            ->get();

        $institutions = Institution::orderBy('name')->get();

        return view('admin.courses.edit', compact('course', 'projects', 'institutions'));
    }

    public function update(CourseRequest $request, Course $course): RedirectResponse
    {
        $this->courseService->updateCourse($course, $request->validated());

        return redirect()->route('admin.courses.index')->with('success', 'Curso atualizado com sucesso!');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Curso excluído com sucesso!');
    }

    public function search(Request $request)
    {
        $term = $request->get('q');

        $results = Course::query()
            ->where('name', 'like', "%{$term}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($results);
    }
}
