<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Services\CourseService;
use App\DataTables\CoursesDataTable;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Unit;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index(CoursesDataTable $dataTable)
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $projects = Project::query()->orderBy('name')->get();
        } elseif ($user->hasRole(['coordenador_geral', 'coordenador_adjunto_geral'])) {
            $projects = Project::query()
                ->where('institution_id', $user->institution_id)
                ->orderBy('name')
                ->get();
        } elseif ($user->hasRole('coordenador_adjunto')) {
            $unitIds = $user->units()->pluck('units.id');
            $projects = Project::query()
                ->whereHas('units', fn ($q) => $q->whereIn('units.id', $unitIds))
                ->orderBy('name')
                ->get();
        } else {
            $projects = collect();
        }

        if ($user->hasRole('admin')) {
            $units = Unit::query()->orderBy('name')->get();
        } elseif ($user->hasRole(['coordenador_geral', 'coordenador_adjunto_geral'])) {
            $units = Unit::query()
                ->where('institution_id', $user->institution_id)
                ->orderBy('name')
                ->get();
        } elseif ($user->hasRole('coordenador_adjunto')) {
            $units = $user->units()->orderBy('name')->get();
        } else {
            $units = collect();
        }

        return $dataTable->render('admin.courses.index', compact('projects', 'units'));
    }

    public function create(): View
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];

        $validated = $request->validate($rules);

        $course = $this->courseService->createCourse($validated);

        if ($request->ajax()) {
            return response()->json($course);
        }

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

    public function search(Request $request)
    {
        $term = $request->get('q');

        $results = Course::query()
            ->where('name', 'like', "%{$term}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id','name']);

        return response()->json($results);
    }
}
