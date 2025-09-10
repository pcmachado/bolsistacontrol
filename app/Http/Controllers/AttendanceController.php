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
            'entry_time' => 'nullable|date_format:H:i',
            'exit_time' => 'nullable|date_format:H:i|after:entry_time',
            'hours' => 'required|integer|min:1',
            'observation' => 'nullable|string',
        ]);

        $holder = auth()->user()->scholarshipHolder;
        $scholarship = $holder->scholarship;

        // Valida vigência
        if ($scholarship->start_date && $request->date < $scholarship->start_date) {
            return back()->withErrors(['date' => 'Data anterior ao início do contrato.']);
        }
        if ($scholarship->end_date && $request->date > $scholarship->end_date) {
            return back()->withErrors(['date' => 'Data posterior ao fim do contrato.']);
        }

        // Valida limite diário
        $dailyHours = $holder->attendances()
            ->whereDate('date', $request->date)
            ->sum('hours');

        if ($dailyHours + $request->hours > $scholarship->max_hours_per_day) {
            return back()->withErrors([
                'hours' => "Você já registrou {$dailyHours}h neste dia. O limite é {$scholarship->max_hours_per_day}h."
            ]);
        }

        // (Opcional) Valida limite mensal
        if ($scholarship->max_hours_per_month) {
            $monthlyHours = $holder->attendances()
                ->whereMonth('date', date('m', strtotime($request->date)))
                ->whereYear('date', date('Y', strtotime($request->date)))
                ->sum('hours');

            if ($monthlyHours + $request->hours > $scholarship->max_hours_per_month) {
                return back()->withErrors([
                    'hours' => "Este mês já foram registradas {$monthlyHours}h. O limite é {$scholarship->max_hours_per_month}h."
                ]);
            }
        }

        // Calcula valor
        $value = $scholarship->hourly_rate
            ? $request->hours * $scholarship->hourly_rate
            : null;

        $holder->attendances()->create([
            'date'             => $request->date,
            'hours'            => $request->hours,
            'calculated_value' => $value,
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
