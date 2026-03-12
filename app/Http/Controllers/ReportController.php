<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Models\Unit;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function unitDetail(Request $request, Unit $unit)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $report = $this->reportService->unitAttendance(
            $unit,
            $month,
            $year
        );

        return view('reports.unit_detail', compact(
            'unit',
            'report',
            'month',
            'year'
        ));
    }
}