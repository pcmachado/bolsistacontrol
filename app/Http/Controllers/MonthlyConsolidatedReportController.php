<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Services\ProjectReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonthlyConsolidatedReportController extends Controller
{
    public function __construct(private readonly ProjectReportService $projectReportService) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $holder = $user->scholarshipHolder;

        $isPdf = $request->boolean('pdf');

        abort_if(! $holder, 403);

        $monthInput = $request->input('month', now()->format('Y-m'));
        if (! is_string($monthInput) || ! preg_match('/^\d{4}-\d{2}$/', $monthInput)) {
            $monthInput = now()->format('Y-m');
        }

        [$year, $month] = array_map('intval', explode('-', $monthInput));
        $period = Carbon::create($year, $month, 1)->startOfMonth();

        $projects = $holder->projects()->with('positions')->get();

        $recordsByProject = AttendanceRecord::query()
            ->selectRaw('project_id, COALESCE(SUM(hours), 0) as registered_hours')
            ->where('scholarship_holder_id', $holder->id)
            ->whereYear('date', $period->year)
            ->whereMonth('date', $period->month)
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id');

        $rows = $projects->map(function ($project) use ($recordsByProject, $holder) {
            $positionId = $project->pivot->position_id;
            $weeklyWorkload = (float) ($project->positions
                ->firstWhere('id', $positionId)
                ?->pivot->weekly_workload ?? 0);

            $expectedHours = $weeklyWorkload * 4;
            $registeredHours = (float) ($recordsByProject->get($project->id)->registered_hours ?? 0);

            return [
                'project_name' => $project->name,
                'expected_hours' => $expectedHours,
                'registered_hours' => $registeredHours,
                'difference_hours' => $registeredHours - $expectedHours,
            ];
        })->values();

        $totals = [
            'expected_hours' => (float) $rows->sum('expected_hours'),
            'registered_hours' => (float) $rows->sum('registered_hours'),
            'difference_hours' => (float) $rows->sum('difference_hours'),
        ];

        $oldestDate = AttendanceRecord::query()
            ->where('scholarship_holder_id', $holder->id)
            ->orderBy('date')
            ->value('date');

        $minMonth = $oldestDate
            ? Carbon::parse($oldestDate)->startOfMonth()
            : $period->copy();

        $viewData = [
            'holder' => $holder,
            'rows' => $rows,
            'totals' => $totals,
            'period' => $period,
            'monthInput' => $period->format('Y-m'),
            'minMonth' => $minMonth->format('Y-m'),
            'reportLayout' => $this->projectReportService->build(
                $projects->first(),
                'monthly',
                [
                    'scholarship_holder' => $user->name,
                    'period' => $period->format('m/Y'),
                    'project' => 'Consolidado',
                    'year' => $period->year,
                    'month' => $period->month,
                ],
                $isPdf
            ),
        ];

        if ($isPdf) {
            $pdf = Pdf::loadView('attendance.reports.monthly_consolidated_pdf', $viewData, ['isPdf' => true]);

            return $request->boolean('download')
                ? $pdf->download("relatorio_consolidado_{$period->format('m_Y')}.pdf")
                : $pdf->stream("relatorio_consolidado_{$period->format('m_Y')}.pdf");
        }

        return view('attendance.reports.monthly_consolidated', $viewData, ['isPdf' => $isPdf]);
    }
}
