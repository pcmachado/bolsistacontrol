<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\ClassOffering;
use App\Models\Discipline;
use App\Models\User;
use App\DataTables\ClassSessionsDataTable;
use Illuminate\Http\Request;

class ClassSessionController extends Controller
{
    public function index(ClassSessionsDataTable $dataTable, ClassOffering $offering)
    {
        $dataTable->setOffering($offering);

        return $dataTable->render('admin.class_offerings.sessions.index', [
            'offering' => $offering,
            'disciplines' => $offering->disciplines,
            'teachers' => $offering->disciplines->pluck('pivot.teacher')->unique('id')->filter(),
        ]);
    }

    public function store(Request $request, ClassOffering $offering)
    {
        $validated = $request->validate([
            'discipline_id' => 'required|exists:disciplines,id',
            'teacher_id'    => 'required|exists:users,id',
            'date'          => 'required|date',
            'start_time'    => 'required',
            'end_time'      => 'required|after:start_time',
            'notes'         => 'nullable|string',
        ]);

        $start = \Carbon\Carbon::parse($validated['start_time']);
        $end   = \Carbon\Carbon::parse($validated['end_time']);

        $validated['duration_hours'] = $start->diffInMinutes($end) / 60;

        $validated['class_offering_id'] = $offering->id;

        ClassSession::create($validated);

        return back()->with('success', 'Aula registrada com sucesso!');
    }

    public function destroy(ClassSession $session)
    {
        $session->delete();

        return back()->with('success', 'Aula removida.');
    }
}
