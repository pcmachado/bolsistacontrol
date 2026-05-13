<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSubmission;
use App\Services\ProjectReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceReportController extends Controller
{
    public function __construct(private readonly ProjectReportService $projectReportService) {}

    public function index(): View
    {
        $user = Auth::user();
        $holder = $user->scholarshipHolder;

        abort_if(! $holder, 403);

        $submissions = AttendanceSubmission::query()
            ->with('project')
            ->where('scholarship_holder_id', $holder->id)
            ->whereIn('status', [
                AttendanceSubmission::STATUS_APPROVED,
                AttendanceSubmission::STATUS_SUBMITTED,
            ])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('attendance.reports.index', compact('submissions'));
    }

    public function monthly(Request $request, AttendanceSubmission $submission)
    {
        $this->authorize('report', $submission);

        $submission->load([
            'project.institution',
            'project.documentTemplate',
            'scholarshipHolder.user',
            'scholarshipHolder.unit.institution',
        ]);

        $records = $this->getRecords($submission);

        if ($request->boolean('pdf')) {
            return $this->renderPdf($submission, $records, $request);
        }

        return view('attendance.reports.monthly', [
            'submission' => $submission,
            'records' => $records,
            'isPdf' => false,
            'reportLayout' => $this->projectReportService->build(
                $submission->project,
                'monthly',
                $this->reportVariables($submission),
                false
            ),
        ]);
    }

    private function getRecords(AttendanceSubmission $submission)
    {
        return $submission->attendanceRecords()
            ->with('project')
            ->orderBy('date')
            ->get();
    }

    private function renderPdf($submission, $records, Request $request)
    {
        $pdf = Pdf::loadView('attendance.reports.monthly', [
            'submission' => $submission,
            'records' => $records,
            'isPdf' => true,
            'reportLayout' => $this->projectReportService->build(
                $submission->project,
                'monthly',
                $this->reportVariables($submission),
                true
            ),
        ]);

        $filename = "relatorio_{$submission->month}_{$submission->year}.pdf";

        if ($request->boolean('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    public function monthlyBlank(Request $request, AttendanceSubmission $submission)
    {
        $this->authorize('report', $submission);

        $submission->load([
            'project.institution',
            'project.documentTemplate',
            'scholarshipHolder.user',
            'scholarshipHolder.unit.institution',
        ]);

        if ($request->boolean('pdf')) {
            $pdf = Pdf::loadView('attendance.reports.monthly_blank', [
                'submission' => $submission,
                'isPdf' => true,
                'reportLayout' => $this->projectReportService->build(
                    $submission->project,
                    'monthly',
                    $this->reportVariables($submission),
                    true
                ),
            ]);

            $filename = "relatorio_blank_{$submission->month}_{$submission->year}.pdf";

            if ($request->boolean('download')) {
                return $pdf->download($filename);
            }

            return $pdf->stream($filename);
        }

        return view('attendance.reports.monthly_blank', [
            'submission' => $submission,
            'isPdf' => false,
            'reportLayout' => $this->projectReportService->build(
                $submission->project,
                'monthly',
                $this->reportVariables($submission),
                false
            ),
        ]);
    }

    private function reportVariables(AttendanceSubmission $submission): array
    {
        return [
            'scholarship_holder' => $submission->scholarshipHolder->user->name ?? '',
            'cpf' => $submission->scholarshipHolder->cpf ?? '',
            'project' => $submission->project?->name ?? '',
            'period' => str_pad($submission->month, 2, '0', STR_PAD_LEFT).'/'.$submission->year,
            'unit' => $submission->scholarshipHolder->unit->name ?? '',
            'institution' => $submission->scholarshipHolder->unit->institution->name ?? '',
            'year' => $submission->year,
            'month' => $submission->month,
        ];
    }
}
