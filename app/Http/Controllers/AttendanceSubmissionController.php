<?php

namespace App\Http\Controllers;

use App\DataTables\AttendanceSubmissionDataTable;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSubmission;
use App\Models\Project;
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
        $projectIds = $user->visibleProjectIds();
        $projects = Project::query()
            ->withoutGlobalScopes()
            ->when($projectIds->isEmpty(), fn ($query) => $query->whereRaw('1=0'))
            ->when($projectIds->isNotEmpty(), fn ($query) => $query->whereIn('id', $projectIds))
            ->orderBy('name')
            ->get();

        $filters = $request->only(['status', 'month', 'unit_id', 'project_id']);
        $submissionCounts = $dashboardService->submissionCounts(
            $user,
            $request->integer('project_id') ?: null,
            'admin'
        );

        $units = Unit::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $user->visibleUnitIds())
            ->orderBy('name')
            ->get();

        return $dataTable
            ->setMode('admin')
            ->setFilters($filters)
            ->render('attendance.submissions.index', [
                'submissionCounts' => $submissionCounts,
                'units' => $units,
                'projects' => $projects,
                'activeProject' => $projects->firstWhere('id', $request->integer('project_id')),
            ]);
    }

    public function show(AttendanceSubmission $submission)
    {
        $this->authorize('view', $submission);

        $submission->load([
            'project',
            'attendanceRecords.project',
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
            'project_id' => ['required', 'integer', 'exists:projects,id'],
        ]);

        $submission = $this->service->createFromMonth($user, $request->month, (int) $request->project_id);

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
            ->route('attendance.submissions.index', ['project_id' => $submission->project_id])
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
            ->route('attendance.submissions.index', ['project_id' => $submission->project_id])
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
