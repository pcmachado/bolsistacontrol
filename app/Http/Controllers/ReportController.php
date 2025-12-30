<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\AttendanceRecord;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\User;
use App\Models\Position;
use App\Models\ProjectPosition;
use App\Models\Institution;
use App\Exports\ReportExport;
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

        $unitsQuery = match (true) {
            $user->hasRole(['admin','coordenador_geral']) => Unit::query(),
            $user->hasRole('coordenador_adjunto')         => $user->units(),
            default                                       => abort(403),
        };

        $units = $unitsQuery->with([
            'scholarshipHolders.user',
            'scholarshipHolders.attendanceRecords' => fn ($q) =>
                $q->whereMonth('date', $month)->whereYear('date', $year),
            'scholarshipHolders.projects' => fn ($q) =>
                $q->withPivot(['position_id', 'start_date', 'end_date', 'status']),
        ])->get();

        $report = $this->buildMonthlyConsolidatedReport($units, $month, $year);

        return view('reports.report', compact('report', 'month', 'year'));
    }

    /**
     * Relatório detalhado de uma unidade.
     */
    public function unitDetail(Request $request, Unit $unit)
    {
        $user = Auth::user();
        $this->authorize('view', $project);

        if ($project->institution_id !== $user->institution_id) {
            abort(403, 'Você não tem acesso a este projeto.');
        }

        // 🔒 Coordenador adjunto só pode acessar sua própria unidade
        if ($user->hasRole('coordenador_adjunto') && !$user->units->contains($unit)) {
            abort(403, 'Você não tem permissão para acessar esta unidade.');
        }

        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        // 🔹 Monta query de attendances
        $attendancesQuery = AttendanceRecord::with(['scholarshipHolder.user'])
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

        $projects = $unit->projects()->with('scholarshipHolders.user')->get();

        $units = Unit::all();
    //dd($report);exit;
        return view('reports.unit_detail', compact('unit', 'report', 'month', 'year', 'units', 'projects'));
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

        if ($request->filled('month') && str_contains($request->month, '-')) {
            [$year, $month] = explode('-', $request->month);
        } else {
            $month = $request->input('month', now()->month);
            $year  = $request->input('year', now()->year);
        }

        $month = (int)$month;
        $year  = (int)$year;

        $attendanceRecords = $holder->attendanceRecords()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date','asc')
            ->get();

         $currentProject = $holder->projects()
            ->withPivot(['weekly_workload','start_date','end_date','status','position_id'])
            ->wherePivot('status', 'active')
            ->first();

        // Se não tiver registros, manda para a view "em branco"
        if ($attendanceRecords->isEmpty()) {

            $data = compact(
                'holder',
                'month',
                'year',
                'currentProject',
            );

            if ($request->boolean('pdf')) {
                $pdf = PDF::loadView('reports.myReport_blank', $data);
                return $pdf->download("individual_report_{$holder->id}_{$month}_{$year}.pdf");
            }

            return view('reports.myReport_blank', $data);
        }

        $semanas = $attendanceRecords->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->date)->weekOfMonth;
            })->map(function($items, $semana) {
                return [
                    'semana'     => $semana,
                    'horas'      => $items->sum('hours'),
                    'atividades' => $items->pluck('observation')->implode('; ')
                ];
            })
            ->sortKeys();

        $totalHoras = $attendanceRecords->sum('hours');

        $data = [
            'holder'         => $holder,
            'month'          => $month,
            'year'           => $year,
            'semanas'        => $semanas,
            'totalHoras'     => $totalHoras,
            'attendanceRecords'=> $attendanceRecords,
            'project' => $currentProject,
        ];

        if ($request->boolean('pdf')) {
            $pdf = PDF::loadView('reports.myReport', $data);
            return $pdf->download("individual_report_{$holder->id}_{$month}_{$year}.pdf");
        }

        return view('reports.myReport', $data);
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

        return Excel::download(new ReportExport($report, $month, $year, $unit),
            "relatorio_{$unit->id}_{$month}_{$year}.xlsx");
    }

    private function buildReport(Request $request, Unit $unit)
    {
        $user = auth()->user();
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $projects = $unit->projects()->with('scholarshipHolders.user')->get();
        $units = Unit::all();

        $attendancesQuery = AttendanceRecord::with(['scholarshipHolder.user', 'scholarshipHolder.scholarship'])
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

        return [$report, $month, $year, $units, $projects];

    }

    private function buildMonthlyConsolidatedReport(
        $units,
        int $month,
        int $year
    ) {
        $periodStart = Carbon::create($year, $month, 1)->startOfMonth();
        $periodEnd   = Carbon::create($year, $month, 1)->endOfMonth();

        $positionRatesCache = [];

        return $units->flatMap(function ($unit) use (
            $periodStart,
            $periodEnd,
            &$positionRatesCache
        ) {
            return $unit->scholarshipHolders->map(function ($holder) use (
                $unit,
                $periodStart,
                $periodEnd,
                &$positionRatesCache
            ) {
                $totalHours = $holder->attendanceRecords->sum('hours');

                $activeProject = $holder->projects->first(function ($project) use ($periodStart, $periodEnd) {
                    $start = $project->pivot->start_date ? Carbon::parse($project->pivot->start_date) : null;
                    $end   = $project->pivot->end_date ? Carbon::parse($project->pivot->end_date) : null;

                    return (!$start || $start <= $periodEnd)
                        && (!$end || $end >= $periodStart);
                });

                $hourlyRate = 0;

                if ($activeProject) {
                    $key = $activeProject->id.'_'.$activeProject->pivot->position_id;

                    if (!isset($positionRatesCache[$key])) {
                        $positionRatesCache[$key] = (float) \DB::table('project_positions')
                            ->where('project_id', $activeProject->id)
                            ->where('position_id', $activeProject->pivot->position_id)
                            ->value('hourly_rate');
                    }

                    $hourlyRate = $positionRatesCache[$key];
                }

                return [
                    'unit'              => $unit->name,
                    'scholarshipHolder' => $holder->user->name,
                    'totalHours'        => $totalHours,
                    'hourlyRate'        => $hourlyRate,
                    'totalValue'        => $totalHours * $hourlyRate,
                ];
            });
        });
    }

}
