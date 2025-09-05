<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder;
use App\Models\Unit;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function create(): View
    {
        $bolsistas = ScholarshipHolder::all();
        $unidades = Unit::all();
        return view('attendance.create', compact('bolsistas', 'unidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'scholarship_holder_id' => 'required|exists:scholarship_holders,id',
            'unit_id' => 'required|exists:units,id',
            'date' => 'required|date',
            'entry_time' => 'required|date_format:H:i',
            'exit_time' => 'nullable|date_format:H:i|after:entry_time',
            'observation' => 'nullable|string',
        ]);

        AttendanceRecord::create($request->all());

        return back()->with('success', 'Registro de frequência salvo com sucesso!');
    }

    /**
     * Exibe a listagem de registros de frequência para homologação.
     * Apenas para coordenadores.
     */
    public function homologarIndex(): View
    {
        // Pega todos os registros de frequência que ainda não foram homologados
        $registros = AttendanceRecord::with(['scholarshipHolder', 'unit'])
            ->where('approved', false) // Supondo que você adicione uma coluna 'approved'
            ->latest()
            ->paginate(20);

        return view('admin.frequencia.homologar', compact('registros'));
    }

    /**
     * Homologa um registro de frequência.
     */
    public function homologar(AttendanceRecord $attendanceRecord)
    {
        $attendanceRecord->update(['approved' => true]);
        // Você pode adicionar mais lógica aqui, como criar uma notificação para o bolsista.
        return back()->with('success', 'Registro homologado com sucesso!');
    }
}
