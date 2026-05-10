<?php

namespace App\Http\Controllers;

use App\DataTables\AttendanceSubmissionDataTable;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSubmission;
use App\Services\AttendanceDashboardService;
use App\Services\AttendanceSubmissionService;
use App\Services\ScholarshipHolderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyAttendanceSubmissionController extends Controller
{
    public function __construct(
        protected AttendanceSubmissionService $service,
        protected ScholarshipHolderService $scholarshipHolderService
    ) {
        $this->middleware('auth');
    }

    public function index(
        Request $request,
        AttendanceSubmissionDataTable $dataTable,
        AttendanceDashboardService $dashboardService
    ) {
        $context = $this->scholarshipHolderService->attendanceContext(
            Auth::user(),
            $request->integer('project_id') ?: null
        );

        $filters = $request->only(['status', 'month']);
        $filters['project_id'] = $context['activeProjectId'];

        return $dataTable
            ->setMode('self')
            ->setFilters($filters)
            ->render('attendance.submissions.my', [
                ...$context,
                'submissionCounts' => $dashboardService->submissionCounts(
                    Auth::user(),
                    $context['activeProjectId'],
                    'self'
                ),
            ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => ['required', 'date_format:Y-m'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
        ]);

        $submission = $this->service->createFromMonth(
            Auth::user(),
            $request->month,
            (int) $request->project_id
        );

        return redirect()
            ->route('my-attendance.submissions.show', $submission)
            ->with('success', 'Submissão criada com sucesso.');
    }

    public function show(AttendanceSubmission $submission)
    {
        $submission = $this->service->findById($submission->id);

        $this->authorize('view', $submission);

        $submission->load([
            'project',
            'attendanceRecords.project',
            'scholarshipHolder.user',
            'scholarshipHolder.unit',
        ]);

        return view('attendance.submissions.show', compact('submission'));
    }

    public function submit(AttendanceSubmission $submission)
    {
        $this->authorize('submit', $submission);

        $this->service->submit($submission);

        return back()->with('success', 'Frequência enviada para homologação.');
    }

    public function approve(AttendanceSubmission $submission)
    {
        $this->authorize('approve', $submission);

        $this->service->approve($submission, Auth::id());

        return redirect()
            ->route('my-attendance.submissions.my', ['project_id' => $submission->project_id])
            ->with('success', 'Submissão homologada com sucesso.');
    }

    public function reject(Request $request, AttendanceSubmission $submission)
    {
        $this->authorize('reject', $submission);

        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->service->reject($submission, $request->reason, Auth::id());

        return redirect()
            ->route('my-attendance.submissions.my', ['project_id' => $submission->project_id])
            ->with('success', 'Submissão rejeitada.');
    }

    public function removeRecord(AttendanceSubmission $submission, AttendanceRecord $record)
    {
        $this->authorize('submit', $submission);

        if ($record->attendance_submission_id !== $submission->id) {
            abort(403);
        }

        $this->service->removeRecord($submission, $record);

        return back()->with('success', 'Registro removido da submissão.');
    }
}
