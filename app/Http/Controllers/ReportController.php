<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PDF;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(): View
    {
        $units = Unit::all();
        return view('reports.index', compact('units'));
    }

    public function gerarRelatorio(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
        ]);

        $unitId = $request->input('unit_id');
        $month = $request->input('month');
        $year = $request->input('year');

        $startOfDay = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endOfDay = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();

        $registros = AttendanceRecord::where('unit_id', $unitId)
            ->whereBetween('date', [$startOfDay, $endOfDay])
            ->with('scholarshipHolder')
            ->get();

        $resumoPorBolsista = $registros->groupBy('scholarship_holder_id')->map(function ($items) {
            $totalHoras = 0;
            foreach ($items as $item) {
                if ($item->entry_time && $item->exit_time) {
                    $entrada = Carbon::parse($item->entry_time);
                    $saida = Carbon::parse($item->exit_time);
                    $totalHoras += $entrada->diffInMinutes($saida) / 60;
                }
            }
            return [
                'scholarship_holder' => $items->first()->scholarshipHolder->nome,
                'total_horas' => round($totalHoras, 2),
            ];
        });

        $unit = Unit::find($unitId);
        $data = [
            'titulo' => "Relatório de Frequência - {$unit->nome}",
            'periodo' => "{$month}/{$year}",
            'resumo' => $resumoPorBolsista,
        ];

        $pdf = PDF::loadView('relatorios.pdf', $data);
        $filename = "relatorio_frequencia_{$unitId}_{$month}_{$year}.pdf";
        Storage::disk('public')->put("relatorios/{$filename}", $pdf->output());

        return response()->json(['filename' => $filename]);
    }
    
    public function download($filename)
    {
        $path = storage_path("app/public/reports/{$filename}");
        if (!Storage::disk('public')->exists("reports/{$filename}")) {
            return response()->json(['message' => 'Arquivo não encontrado.'], 404);
        }
        return response()->download($path);
    }

    public function monthlyReport(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $units = \App\Models\Unit::with(['scholarshipHolders.attendances' => function ($q) use ($month, $year) {
            $q->whereMonth('date', $month)->whereYear('date', $year);
        }])->get();

        $report = $units->map(function ($unit) use ($month, $year) {
            $totalHours = 0;
            $totalValue = 0;

            foreach ($unit->scholarshipHolders as $holder) {
                foreach ($holder->attendances as $attendance) {
                    $totalHours += $attendance->hours;
                    $totalValue += $attendance->calculated_value ?? 0;
                }
            }

            return [
                'unit'       => $unit->name,
                'month'      => $month,
                'year'       => $year,
                'totalHours' => $totalHours,
                'totalValue' => $totalValue,
            ];
        });

        return view('reports.monthly', compact('report', 'month', 'year'));
    }

    public function unitDetail(Request $request, \App\Models\Unit $unit)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $holders = $unit->scholarshipHolders()
            ->with(['user', 'scholarship', 'attendances' => function ($q) use ($month, $year) {
                $q->whereMonth('date', $month)->whereYear('date', $year);
            }])
            ->get();

        $report = $holders->map(function ($holder) use ($month, $year) {
            $totalHours = $holder->attendances->sum('hours');
            $valuePerHour = $holder->scholarship->value_per_hour;
            $totalValue = $totalHours * $valuePerHour;

            return [
                'holder'      => $holder->user->name,
                'scholarship' => $holder->scholarship->name,
                'totalHours'  => $totalHours,
                'valuePerHour'=> $valuePerHour,
                'totalValue'  => $totalValue,
            ];
        });

        return view('reports.unit_detail', compact('unit', 'report', 'month', 'year'));
    }

    public function individualReport(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $holder = Auth::user()->scholarshipHolder;
dd(Auth::user());
        $attendances = $holder->attendances()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        // Agrupar por semana
        $semanas = $attendances->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->date)->weekOfMonth;
        })->map(function($items, $semana) {
            return [
                'semana' => $semana,
                'horas' => $items->sum('hours'),
                'atividades' => $items->pluck('observation')->implode('; ')
            ];
        });

        $totalHoras = $attendances->sum('hours');

        $data = [
            'holder' => $holder,
            'month' => $month,
            'year' => $year,
            'semanas' => $semanas,
            'totalHoras' => $totalHoras,
        ];

        $pdf = \PDF::loadView('reports.myReport', $data);
        return $pdf->download("individual_report_{$holder->id}_{$month}_{$year}.pdf");
    }
}
