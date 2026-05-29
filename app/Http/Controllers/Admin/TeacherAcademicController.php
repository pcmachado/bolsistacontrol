<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOfferingDiscipline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherAcademicController extends Controller
{
    public function courses(Request $request)
    {
        $teacherId = $this->teacherId();
        $search = $request->string('search')->trim()->toString();

        $pivots = $this->teacherDisciplinesQuery($teacherId)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('classOffering.course', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('classOffering.project', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('classOffering', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('discipline', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->get();

        $courses = $pivots
            ->groupBy(fn ($pivot) => $pivot->classOffering?->course?->id)
            ->filter(fn ($items, $courseId) => filled($courseId))
            ->map(function ($items) {
                $course = $items->first()->classOffering->course;
                $offerings = $items->pluck('classOffering')->filter()->unique('id')->values();
                $disciplines = $items->pluck('discipline')->filter()->unique('id')->sortBy('name')->values();

                return [
                    'course' => $course,
                    'offerings' => $offerings,
                    'disciplines' => $disciplines,
                    'projects' => $offerings->pluck('project')->filter()->unique('id')->sortBy('name')->values(),
                    'students_count' => $offerings->sum(fn ($offering) => $offering->students->count()),
                ];
            })
            ->sortBy(fn ($item) => $item['course']->name)
            ->values();

        return view('teacher.courses.index', compact('courses', 'search'));
    }

    public function disciplines(Request $request)
    {
        $teacherId = $this->teacherId();
        $search = $request->string('search')->trim()->toString();

        $pivots = $this->teacherDisciplinesQuery($teacherId)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('discipline', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('classOffering.course', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('classOffering.project', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('classOffering', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->get();

        $disciplines = $pivots
            ->groupBy('discipline_id')
            ->map(function ($items) {
                $discipline = $items->first()->discipline;
                $offerings = $items->pluck('classOffering')->filter()->unique('id')->values();

                return [
                    'discipline' => $discipline,
                    'offerings' => $offerings,
                    'courses' => $offerings->pluck('course')->filter()->unique('id')->sortBy('name')->values(),
                    'projects' => $offerings->pluck('project')->filter()->unique('id')->sortBy('name')->values(),
                    'planned_hours' => $items->sum(fn ($item) => $item->planned_total_hours ?? $item->workload ?? 0),
                    'planned_days' => $items->sum(function ($item) {
                        $hoursPerDay = (float) ($item->hours_per_day ?? $item->classOffering?->hours_per_day ?? 0);
                        $hours = (float) ($item->planned_total_hours ?? $item->workload ?? 0);

                        return $hoursPerDay > 0 ? (int) ceil($hours / $hoursPerDay) : 0;
                    }),
                ];
            })
            ->sortBy(fn ($item) => $item['discipline']->name)
            ->values();

        return view('teacher.disciplines.index', compact('disciplines', 'search'));
    }

    private function teacherDisciplinesQuery(int $teacherId)
    {
        return ClassOfferingDiscipline::query()
            ->where('teacher_id', $teacherId)
            ->with([
                'discipline.course',
                'classOffering.course',
                'classOffering.project',
                'classOffering.unit',
                'classOffering.students',
            ])
            ->orderBy('discipline_id');
    }

    private function teacherId(): int
    {
        $user = Auth::user();

        abort_unless($user?->scholarshipHolder, 403, 'Este usuario nao possui acesso como professor.');

        return (int) $user->scholarshipHolder->id;
    }
}
