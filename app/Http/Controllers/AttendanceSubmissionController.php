<?php

namespace App\Http\Controllers;

use App\DataTables\AttendanceSubmissionDataTable;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSubmission;
use App\Models\Unit;
use App\Services\AttendanceDashboardService;
use App\Services\AttendanceSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceSubmissionController extends Controller
{
    public function __construct(
        protected AttendanceSubmissionService $service
    ) {
        $this->middleware('auth');
    }

    public function index(
        Request $request,
        AttendanceSubmissionDataTable $dataTable,
        AttendanceDashboardService $dashboardService
    ) {
        $user = Auth::user();
        $filters = $request->only(['status', 'month', 'unit_id']);
        $submissionCounts = $dashboardService->submissionCounts($user, 'admin');

        $units = Unit::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $user->visibleUnitIds())
            ->orderBy('name')
            ->get();

        return $dataTable
            ->setMode('admin')
            ->setFilters($filters)
            ->render('attendance.submissions.index', compact('submissionCounts', 'units'));
    }

    public function show(AttendanceSubmission $submission)
    {
        $this->authorize('view', $submission);

        $submission->load([
            'attendanceRecords',
            'scholarshipHolder.user',
            'scholarshipHolder.unit',
        ]);

        return view('attendance.submissions.show', compact('submission'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $submission = $this->service->createFromMonth($user, $request->month);

        return redirect()
            ->route('my-attendance.submissions.show', $submission)
            ->with('success', 'Submissao criada com sucesso.');
    }

    public function submit(AttendanceSubmission $submission)
    {
        $this->authorize('submit', $submission);

        $this->service->submit($submission);

        return back()->with('success', 'Frequencia enviada para homologacao.');
    }

    public function approve(AttendanceSubmission $submission)
    {
        $this->authorize('approve', $submission);

        $this->service->approve($submission, Auth::user()->id);

        return redirect()
            ->route('attendance.submissions.index')
            ->with('success', 'Submissao homologada com sucesso.');
    }

    public function reject(Request $request, AttendanceSubmission $submission)
    {
        $user = Auth::user();

        $this->authorize('reject', $submission);

        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->service->reject($submission, $request->reason, $user->id);

        return redirect()
            ->route('attendance.submissions.index')
            ->with('success', 'Submissao rejeitada e devolvida ao bolsista.');
    }

    public function removeRecord(AttendanceSubmission $submission, AttendanceRecord $record)
    {
        $this->authorize('submit', $submission);

        if ($record->attendance_submission_id !== $submission->id) {
            abort(403);
        }

        $this->service->removeRecord($submission, $record);

        return back()->with('success', 'Registro removido da submissao.');
    }
}
