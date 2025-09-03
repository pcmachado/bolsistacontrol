<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        $registros = RegistroFrequencia::where('unit_id', $unitId)
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
        $path = storage_path("app/public/relatorios/{$filename}");
        if (!Storage::disk('public')->exists("relatorios/{$filename}")) {
            return response()->json(['message' => 'Arquivo não encontrado.'], 404);
        }
        return response()->download($path);
    }
}
