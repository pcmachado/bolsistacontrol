<?php

namespace App\Http\Controllers;

use App\DataTables\AttendanceSubmissionDataTable;
use App\Models\AttendanceSubmission;
use App\Models\AttendanceRecord;
use App\Models\Unit;
use App\Services\AttendanceSubmissionService;
use App\Services\AttendanceDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceSubmissionController extends Controller
{
    public function __construct(
        protected AttendanceSubmissionService $service
    ) {
        $this->middleware('auth');
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAGEM ADMINISTRATIVA
    |--------------------------------------------------------------------------
    */

    public function index(Request $request, AttendanceSubmissionDataTable $dataTable, AttendanceDashboardService $dashboardService)
    {

        $user = Auth::user();

        $filters = $request->only(['status', 'month', 'unit_id']);

        $submissionCounts = $dashboardService->submissionCounts($user);

        // Unidades visíveis ao usuário
        if ($user->hasRole('admin')) {
            $units = Unit::orderBy('name')->get();
        } elseif ($user->hasRole(['coordenador_geral','coordenador_adjunto_geral'])) {
            $units = Unit::where('institution_id', $user->institution_id)
                        ->orderBy('name')
                        ->get();
        } elseif ($user->hasRole('coordenador_adjunto')) {
            $units = $user->units()->orderBy('name')->get();
        } else {
            $units = collect();
        }

        return $dataTable
            ->setMode('admin')
            ->setFilters($filters)
            ->render(
                'attendance.submissions.index',
                compact('submissionCounts','units')
            );
    }

    /*
    |--------------------------------------------------------------------------
    | VISUALIZAR
    |--------------------------------------------------------------------------
    */

    public function show(AttendanceSubmission $submission)
    {
        $this->authorize('view', $submission);

        $submission->load([
            'records',
            'scholarshipHolder.user',
            'scholarshipHolder.unit',
        ]);

        return view('attendance.submissions.show', compact('submission'));
    }

    /*
    |--------------------------------------------------------------------------
    | CRIAR SUBMISSÃO (BOLSISTA)
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $submission = $this->service->createFromMonth(
            $user,
            $request->month
        );

        return redirect()
            ->route('attendance.submissions.show', $submission)
            ->with('success', 'Submissão criada com sucesso.');
    }

    /*
    |--------------------------------------------------------------------------
    | ENVIAR
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | APROVAR
    |--------------------------------------------------------------------------
    */

    public function approve(AttendanceSubmission $submission)
    {
        $this->authorize('approve', $submission);

        $this->service->approve($submission, Auth::user()->id);

        return redirect()
            ->route('attendance.submissions.index')
            ->with('success', 'Submissão homologada com sucesso.');
    }

    /*
    |--------------------------------------------------------------------------
    | REJEITAR
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        AttendanceSubmission $submission
    ) {
        $user = Auth::user();

        $this->authorize('reject', $submission);

        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->service->reject(
            $submission,
            $request->reason,
            $user->id
        );

        return redirect()
            ->route('attendance.submissions.index')
            ->with(
                'success',
                'Submissão rejeitada e devolvida ao bolsista.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVER REGISTRO DA SUBMISSÃO
    |--------------------------------------------------------------------------
    */

    public function removeRecord(
        AttendanceSubmission $submission,
        AttendanceRecord $record
    ) {

        $this->authorize('submit', $submission);

        // 🔒 Garante que o registro pertence à submissão
        if ($record->attendance_submission_id !== $submission->id) {
            abort(403);
        }

        $this->service->removeRecord($submission, $record);

        return back()->with(
            'success',
            'Registro removido da submissão.'
        );
    }
}
