<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportExport implements FromView
{
    protected $report;
    protected $month;
    protected $year;
    protected $unit;

    public function __construct($report, $month, $year, $unit = null)
    {
        $this->report = $report;
        $this->month = $month;
        $this->year = $year;
        $this->unit = $unit;
    }

    public function view(): View
    {
        return view('attendance.homologation.report_excel', [
            'report' => $this->report,
            'month' => $this->month,
            'year' => $this->year,
            'unit' => $this->unit,
        ]);
    }
}
