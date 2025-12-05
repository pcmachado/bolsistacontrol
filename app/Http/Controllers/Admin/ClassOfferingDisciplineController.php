<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\ClassOfferingDiscipline;
use App\Models\Discipline;
use App\Models\User;
use App\DataTables\ClassOfferingsDataTable;
use Illuminate\Http\Request;

class ClassOfferingDisciplineController extends Controller
{
    public function index(ClassOffering $offering)
    {
        // Carregar disciplinas e pivot
        $offering->load(['disciplines', 'course.disciplines']);

        // Carregar professor do pivot manualmente
        $offering->disciplines->each(function ($disc) {
            if ($disc->pivot) {
                $disc->pivot->load('teacher');
            }
        });

        return view('admin.class_offerings.disciplines.index', [
            'offering'    => $offering,
            'disciplines' => $offering->course->disciplines,
            'teachers'    => User::role('professor')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, ClassOffering $offering)
    {
        $request->validate([
            'discipline_id' => 'required|exists:disciplines,id',
        ]);

        // Evita duplicação
        if ($offering->disciplines()->where('discipline_id', $request->discipline_id)->exists()) {
            return back()->with('warning', 'A disciplina já está vinculada à turma.');
        }

        $offering->disciplines()->attach($request->discipline_id);

        return back()->with('success', 'Disciplina vinculada à turma!');
    }

    public function update(Request $request, ClassOfferingDiscipline $pivot)
    {
        $validated = $request->validate([
            'teacher_id' => 'nullable|exists:users,id',
            'workload'   => 'nullable|integer|min:1',
            'schedule'   => 'nullable|string|max:255',
            'room'       => 'nullable|string|max:255',
        ]);

        $pivot->update($validated);

        return back()->with('success', 'Disciplina atualizada!');
    }

    public function destroy(ClassOfferingDiscipline $pivot)
    {
        $pivot->delete();

        return back()->with('success', 'Disciplina removida da turma!');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

}
