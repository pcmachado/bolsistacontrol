<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\ClassSession;
use Illuminate\Http\Request;

class ClassSessionController extends Controller
{
    public function index(ClassOffering $offering)
    {
        $offering->load([
            'sessions.discipline',
            'disciplines'
        ]);

        return view('admin.class-offerings.sessions.index', [
            'offering'    => $offering,
            'sessions'    => $offering->sessions,
            'disciplines' => $offering->disciplines,
        ]);
    }

    public function store(Request $request, ClassOffering $offering)
    {
        $validated = $request->validate([
            'discipline_id' => ['required', 'exists:disciplines,id'],
            'date'          => ['required', 'date'],
            'start_time'    => ['required'],
            'end_time'      => ['required'],
            'workload'      => ['required', 'integer', 'min:1'],
            'room'          => ['nullable', 'string'],
            'notes'         => ['nullable', 'string'],
        ]);

        $offering->sessions()->create($validated);

        return back()->with('success', 'Aula registrada com sucesso.');
    }

    public function destroy(ClassOffering $offering, ClassSession $session)
    {
        abort_if($session->class_offering_id !== $offering->id, 403);

        $session->delete();

        return back()->with('success', 'Aula removida.');
    }
}
