<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceSubmission;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceReportController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $holder = $user->scholarshipHolder;

        abort_if(! $holder, 403);

        $submissions = AttendanceSubmission::query()
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

        // 🔹 Carrega relações
        $submission->load([
            'scholarshipHolder.user',
            'scholarshipHolder.unit',
            'scholarshipHolder.projects',
        ]);

        // 🔹 Busca registros
        $records = $this->getRecords($submission);

        // 🔹 Se for PDF
        if ($request->boolean('pdf')) {
            return $this->renderPdf($submission, $records, $request);
        }

        // 🔹 HTML normal
        return view('attendance.reports.monthly', [
            'submission' => $submission,
            'records' => $records,
            'isPdf' => false
        ]);
    }

    /**
     * 🔎 Centraliza busca de registros
     */
    private function getRecords(AttendanceSubmission $submission)
    {
        return AttendanceRecord::where('scholarship_holder_id', $submission->scholarship_holder_id)
            ->whereMonth('date', $submission->month)
            ->whereYear('date', $submission->year)
            ->orderBy('date')
            ->get();
    }

    /**
     * 📄 Renderização PDF padronizada
     */
    private function renderPdf($submission, $records, Request $request)
    {
        $pdf = Pdf::loadView('attendance.reports.monthly', [
            'submission' => $submission,
            'records' => $records,
            'isPdf' => true
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
            'scholarshipHolder.user',
            'scholarshipHolder.unit',
            'scholarshipHolder.projects',
        ]);

        if ($request->boolean('pdf')) {
            $pdf = Pdf::loadView('attendance.reports.monthly_blank', [
                'submission' => $submission,
                'isPdf' => true,
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
        ]);
    }
}