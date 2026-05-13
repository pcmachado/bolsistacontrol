<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Discipline;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CourseDisciplineController extends Controller
{
    public function index(Course $course)
    {
        $course->load('disciplines');
        $disciplines = Discipline::all();

        $disciplinesJson = $disciplines->map(function ($discipline) {

            return [
                'id' => $discipline->id,
                'name' => $discipline->name,
                'code' => $discipline->code,
                'active' => $discipline->active,
            ];

        })->values();

        $selectedDisciplinesJson = $course->disciplines
            ->mapWithKeys(function ($discipline) {

                return [
                    $discipline->id => [
                        'id' => $discipline->id,
                        'name' => $discipline->name,
                        'code' => $discipline->code,
                        'active' => $discipline->active,
                    ]
                ];

            });

        return view(
            'admin.courses.disciplines.index',
            compact('course', 'disciplines', 'disciplinesJson', 'selectedDisciplinesJson')
        );
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'disciplines'   => ['nullable', 'array'],
            'disciplines.*' => ['exists:disciplines,id'],
        ]);

        DB::transaction(function () use ($course, $validated) {
            $ids = $validated['disciplines'] ?? [];

            if (empty($ids)) {
                return;
            }

            Discipline::query()
                ->whereIn('id', $ids)
                ->update(['course_id' => $course->id]);
        });

        return redirect()
            ->route('admin.courses.disciplines.index', $course)
            ->with('success', 'Disciplinas atualizadas com sucesso.');
    }
}
