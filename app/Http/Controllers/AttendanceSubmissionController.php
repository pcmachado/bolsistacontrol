<?php

namespace App\Http\Controllers;

use App\DataTables\AttendanceSubmissionDataTable;
use App\Models\AttendanceSubmission;
use App\Services\AttendanceSubmissionService;
use App\Services\AttendanceDashboardService;
use Illuminate\Http\Request;

class AttendanceSubmissionController extends Controller
{
    public function __construct(
        protected AttendanceSubmissionService $service
    ) {
        $this->middleware('auth');
    }

    /**
     * Listagem (bolsista / coordenação)
     */
    public function index(AttendanceSubmissionDataTable $dataTable, AttendanceDashboardService $dashboardService) {
        $filters = request()->only(['status', 'month', 'unit_id']);

        if (auth()->user()->hasRole('bolsista')) {
            unset($filters['month']);
        }

        $submissionCounts = $dashboardService
            ->submissionCounts(auth()->user());

        return $dataTable
            ->setFilters($filters)
            ->render(
            'attendance.submissions.index',
            compact('submissionCounts')
        );
    }

    /**
     * Visualizar submissão (agrupada)
     */
    public function show(AttendanceSubmission $submission)
    {
        $this->authorize('view', $submission);

        $submission->load([
            'records',
            'scholarshipHolder.user',
        ]);

        return view('attendance.submissions.show', compact('submission'));
    }

    /**
     * Criar submissão do mês (bolsista)
     */
    public function store(Request $request)
    {
        $request->validate([
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $submission = $this->service->createFromMonth(
            auth()->user(),
            $request->month
        );

        return redirect()
            ->route('attendance.submissions.show', $submission)
            ->with('success', 'Submissão criada com sucesso.');
    }

    /**
     * Enviar para homologação
     */
    public function submit(AttendanceSubmission $submission)
    {
        $this->authorize('submit', $submission);

        $this->service->submit($submission);

        return back()->with(
            'success',
            'Frequência enviada para homologação.'
        );
    }

    /**
     * Aprovar submissão
     */
    public function approve(AttendanceSubmission $submission)
    {
        $this->authorize('approve', $submission);

        $this->service->approve($submission, auth()->id());

        return redirect()
        ->route('attendance.submissions.index')
        ->with('success', 'Submissão homologada com sucesso.');
    }

    /**
     * Rejeitar submissão
     */
    public function reject(Request $request, AttendanceSubmission $submission)
    {
        $this->authorize('reject', $submission);

        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->service->reject(
            $submission,
            $request->reason,
            auth()->user()
            
        );

        return redirect()
        ->route('attendance.submissions.index')
        ->with('success', 'Submissão rejeitada e devolvida ao bolsista.');
    }

    public function my(AttendanceSubmissionDataTable $dataTable)
    {
        return $dataTable->render('attendance.submissions.my');
    }

    public function removeRecord(AttendanceSubmission $submission, AttendanceRecord $record)
    {
        $this->authorize('submit', $submission);

        $this->service->removeRecord($submission, $record);

        return back()->with('success', 'Registro removido da submissão.');
    }

}
