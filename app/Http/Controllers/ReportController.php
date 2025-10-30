<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

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
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);
        $user  = Auth::user();

        // Carrega unidades conforme papel
        if ($user->hasRole(['admin','coordenador_geral'])) {
            $unitsQuery = Unit::query();
        } elseif ($user->hasRole('coordenador_adjunto')) {
            $unitsQuery = $user->units();
        } else {
            abort(403, 'Acesso negado.');
        }

        // Eager loading dos holders + user + attendances no mês/ano
        $units = $unitsQuery
            ->with([
                'scholarshipHolders.user',
                'scholarshipHolders.attendanceRecords' => function ($q) use ($month, $year) {
                    $q->whereMonth('date', $month)->whereYear('date', $year);
                },
                // Para resolver valor/hora via projeto/cargo:
                // Carrega os projetos vinculados ao bolsista com dados da pivot (position_id, datas)
                'scholarshipHolders.projects' => function ($q) {
                    $q->withPivot(['position_id', 'start_date', 'end_date', 'status']);
                },
            ])
            ->get();

        // Mês/ano como período Date para comparar intervalo do vínculo do projeto
        $periodStart = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $periodEnd   = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();

        // Para reduzir consultas, vamos preparar um cache local de rates por projeto/cargo
        $positionRatesCache = [];

        $report = $units->flatMap(function ($unit) use ($periodStart, $periodEnd, &$positionRatesCache) {
            return $unit->scholarshipHolders->map(function ($holder) use ($unit, $periodStart, $periodEnd, &$positionRatesCache) {
                $totalHours = $holder->attendanceRecords->sum('hours');

                // Descobrir o vínculo de projeto "ativo" no período
                // Regra: vínculo cujo intervalo [start_date, end_date] intersecta o mês.
                $activeProject = $holder->projects->first(function ($project) use ($periodStart, $periodEnd) {
                    $start = $project->pivot->start_date ? \Carbon\Carbon::parse($project->pivot->start_date) : null;
                    $end   = $project->pivot->end_date ? \Carbon\Carbon::parse($project->pivot->end_date) : null;

                    // Considera sem end_date como aberto
                    $inRangeStart = $start ? $start <= $periodEnd : true;
                    $inRangeEnd   = $end   ? $end   >= $periodStart : true;

                    return $inRangeStart && $inRangeEnd;
                });

                // Se não houver projeto ativo, considera rate 0
                $hourlyRate = 0.0;

                if ($activeProject) {
                    $projectId  = $activeProject->id;
                    $positionId = $activeProject->pivot->position_id;

                    if ($projectId && $positionId) {
                        // Cache key
                        $key = $projectId.'_'.$positionId;

                        if (!array_key_exists($key, $positionRatesCache)) {
                            // Busca rate na pivot project_position
                            $rate = \DB::table('project_positions')
                                ->where('project_id', $projectId)
                                ->where('position_id', $positionId)
                                ->value('hourly_rate');

                            $positionRatesCache[$key] = (float) ($rate ?? 0);
                        }

                        $hourlyRate = $positionRatesCache[$key];
                    }
                }

                $totalValue = $totalHours * $hourlyRate;

                // Horas previstas (se existir limite semanal)
                $expectedHours = $holder->weekly_limit_minutes
                    ? ($holder->weekly_limit_minutes / 60) * 4
                    : null;

                return [
                    'unit'             => $unit->name,
                    'scholarshipHolder'=> $holder->user->name,
                    'expected_hours'   => $expectedHours,
                    'totalHours'       => $totalHours,
                    'hourlyRate'       => $hourlyRate,
                    'totalValue'       => $totalValue,
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

        $project = Project::findOrFail($projectId);

        // 🔒 Coordenador adjunto só pode acessar sua própria unidade
        if ($user->hasRole('coordenador_adjunto') && !$user->units->contains($unit)) {
            abort(403, 'Você não tem permissão para acessar esta unidade.');
        }

        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        // 🔹 Monta query de attendances
        $attendancesQuery = \App\Models\AttendanceRecord::with(['scholarshipHolder.user'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        // 🔒 Coordenador adjunto → restringe à unidade do usuário
        if ($user->hasRole('coordenador_adjunto')) {
            $attendancesQuery->whereHas('scholarshipHolder', fn($q) =>
                $q->where('unit_id', $user->unit_id)
            );
        }

        // 🔓 Coordenador geral/admin → pode filtrar por unidade
        if ($request->filled('unit_id')) {
            $attendancesQuery->whereHas('scholarshipHolder', fn($q) =>
                $q->where('unit_id', $request->unit_id)
            );
        }

        // 🔹 Filtro por status
        if ($request->filled('status')) {
            $attendancesQuery->where('status', $request->status);
        }

        $attendances = $attendancesQuery->get();

        // 🔹 Agrupa por bolsista
        $report = $attendances->groupBy('scholarship_holder_id')->map(function ($records) use ($project) {
            $holder = $records->first()->scholarshipHolder;

            // pega o cargo do bolsista no projeto
            $positionId = $holder->projects()
                ->where('project_id', $project->id)
                ->first()?->pivot->position_id;

            // pega o valor/hora da pivot project_position
            $hourlyRate = $project->positions()
                ->where('positions.id', $positionId)
                ->first()?->pivot->hourly_rate ?? 0;

            $totalHours = $records->sum('hours');
            $totalValue = $totalHours * $hourlyRate;

            return [
                'unit'            => $holder->units->first()->name ?? null,
                'scholarshipHolder' => $holder->user->name,
                'phone'           => $holder->phone,
                'cpf'             => $holder->cpf,
                'bank'            => $holder->bank,
                'agency'          => $holder->agency,
                'account'         => $holder->account,
                'expected_hours'  => $holder->weekly_limit_minutes
                                        ? ($holder->weekly_limit_minutes / 60) * 4
                                        : null,
                'totalHours'      => $totalHours,
                'hourlyRate'      => $hourlyRate,
                'totalValue'      => $totalValue,
            ];
        });

        $units = Unit::all();
    //dd($report);exit;
        return view('reports.unit_detail', compact('unit', 'report', 'month', 'year', 'units'));
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

        $attendanceRecords = $holder->attendanceRecords()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $semanas = $attendanceRecords->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->date)->weekOfMonth;
        })->map(function($items, $semana) {
            return [
                'semana'     => $semana,
                'horas'      => $items->sum('hours'),
                'atividades' => $items->pluck('observation')->implode('; ')
            ];
        });

        $totalHoras = $attendanceRecords->sum('hours');

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

    public function reportPdf(Request $request, Unit $unit)
    {
        [$report, $month, $year, $units] = $this->buildReport($request, $unit);

        $pdf = Pdf::loadView('reports.report_pdf', compact('report', 'month', 'year', 'unit', 'units'));
        return $pdf->download("relatorio_{$unit->id}_{$month}_{$year}.pdf");
    }

    public function reportExcel(Request $request, Unit $unit)
    {
        [$report, $month, $year, $units] = $this->buildReport($request, $unit);

        return Excel::download(new \App\Exports\ReportExport($report, $month, $year, $unit), 
            "relatorio_{$unit->id}_{$month}_{$year}.xlsx");
    }

    private function buildReport(Request $request, Unit $unit)
    {
        $user = auth()->user();
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);
        $units = Unit::all();

        $attendancesQuery = \App\Models\AttendanceRecord::with(['scholarshipHolder.user', 'scholarshipHolder.scholarship'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        if ($user->hasRole('coordenador_adjunto')) {
            $attendancesQuery->whereHas('scholarshipHolder', fn($q) =>
                $q->where('unit_id', $user->unit_id)
            );
        }

        if ($request->filled('unit_id')) {
            $attendancesQuery->whereHas('scholarshipHolder', fn($q) =>
                $q->where('unit_id', $request->unit_id)
            );
        } else {
            $attendancesQuery->whereHas('scholarshipHolder', fn($q) =>
                $q->where('unit_id', $unit->id)
            );
        }

        if ($request->filled('status')) {
            $attendancesQuery->where('status', $request->status);
        }

        $attendances = $attendancesQuery->get();

        $report = $attendances->groupBy('scholarship_holder_id')->map(function ($records) use ($project) {
            $holder = $records->first()->scholarshipHolder;

            // pega o cargo do bolsista no projeto
            $positionId = $holder->projects()
                ->where('project_id', $project->id)
                ->first()?->pivot->position_id;

            // pega o valor/hora da pivot project_position
            $hourlyRate = $project->positions()
                ->where('positions.id', $positionId)
                ->first()?->pivot->hourly_rate ?? 0;

            $totalHours = $records->sum('hours');
            $totalValue = $totalHours * $hourlyRate;

            return [
                'unit'            => $holder->units->first()->name ?? null,
                'scholarshipHolder' => $holder->user->name,
                'phone'           => $holder->phone,
                'cpf'             => $holder->cpf,
                'bank'            => $holder->bank,
                'agency'          => $holder->agency,
                'account'         => $holder->account,
                'expected_hours'  => $holder->weekly_limit_minutes
                                        ? ($holder->weekly_limit_minutes / 60) * 4
                                        : null,
                'totalHours'      => $totalHours,
                'hourlyRate'      => $hourlyRate,
                'totalValue'      => $totalValue,
            ];
        });

        return [$report, $month, $year, $units];

    }

}
