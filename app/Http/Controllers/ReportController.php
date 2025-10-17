<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PDF;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Tela inicial de relatórios (filtros).
     */
    public function index(): View
    {
        $user = Auth::user();

        if ($user->hasRole(['admin','coordenador_geral'])) {
            $units = Unit::all();
        } elseif ($user->hasRole('coordenador_adjunto')) {
            $units = $user->units; // relacionamento user->units()
        } else {
            $units = collect(); // bolsista não deve ver aqui
        }

        return view('reports.index', compact('units'));
    }

    /**
     * Relatório mensal consolidado (todas as unidades ou apenas as do adjunto).
     */
    public function monthlyReport(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $user = Auth::user();

        if ($user->hasRole(['admin','coordenador_geral'])) {
            $units = Unit::with(['scholarshipHolders.attendanceRecords' => function ($q) use ($month, $year) {
                $q->whereMonth('date', $month)->whereYear('date', $year);
            }])->get();
        } elseif ($user->hasRole('coordenador_adjunto')) {
            $units = $user->units()->with(['scholarshipHolders.attendanceRecords' => function ($q) use ($month, $year) {
                $q->whereMonth('date', $month)->whereYear('date', $year);
            }])->get();
        } else {
            abort(403, 'Acesso negado.');
        }

        $report = $units->flatMap(function ($unit) {
            return $unit->scholarshipHolders->map(function ($holder) use ($unit) {
                $totalHours   = $holder->attendanceRecords->sum('hours');
                $valuePerHour = $holder->scholarship->value_per_hour ?? 0;
                $totalValue   = $totalHours * $valuePerHour;

                return [
                    'scholarshipHolder' => $holder->user->name,
                    'totalHours'        => $totalHours,
                    'totalValue'        => $totalValue,
                ];
            });
        });

        return view('reports.report', compact('report', 'month', 'year'));
    }

    /**
     * Relatório detalhado de uma unidade.
     */
    public function unitDetail(Request $request, Unit $unit)
    {
        $user = Auth::user();

        if ($user->hasRole('coordenador_adjunto') && !$user->units->contains($unit)) {
            abort(403, 'Você não tem permissão para acessar esta unidade.');
        }
        
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $holders = $unit->scholarshipHolders()
            ->with(['user', 'scholarship', 'attendances' => function ($q) use ($month, $year) {
                $q->whereMonth('date', $month)->whereYear('date', $year);
            }])
            ->get();

        $report = $holders->map(function ($holder) {
            $totalHours   = $holder->attendances->sum('hours');
            $valuePerHour = $holder->scholarship->value_per_hour;
            $totalValue   = $totalHours * $valuePerHour;

            return [
                'scholarshipHolder' => $holder->user->name,   // 👈 agora bate com o Blade
                'totalHours'        => $totalHours,
                'totalValue'        => $totalValue,
            ];
        });

        return view('reports.report', compact('unit', 'report', 'month', 'year'));
    }

    /**
     * Relatório individual do bolsista (PDF).
     */
    public function individualReport(Request $request)
    {
        $user = Auth::user();
        $holder = $user->scholarshipHolder;

        if (!$holder) {
            abort(403, 'Apenas bolsistas podem gerar relatório individual.');
        }

        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $attendances = $holder->attendances()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $semanas = $attendances->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->date)->weekOfMonth;
        })->map(function($items, $semana) {
            return [
                'semana'     => $semana,
                'horas'      => $items->sum('hours'),
                'atividades' => $items->pluck('observation')->implode('; ')
            ];
        });

        $totalHoras = $attendances->sum('hours');

        $data = [
            'holder'     => $holder,
            'month'      => $month,
            'year'       => $year,
            'semanas'    => $semanas,
            'totalHoras' => $totalHoras,
        ];

        $pdf = PDF::loadView('reports.myReport', $data);
        return $pdf->download("individual_report_{$holder->id}_{$month}_{$year}.pdf");
    }
}
