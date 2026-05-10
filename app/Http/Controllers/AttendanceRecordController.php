<?php

namespace App\Http\Controllers;

use App\DataTables\AttendanceRecordDataTable;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSubmission;
use App\Services\AttendanceRecordService;
use App\Services\AttendanceService;
use App\Services\AttendanceSubmissionService;
use App\Services\ScholarshipHolderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceRecordController extends Controller
{
    public function __construct(
        protected AttendanceRecordService $records,
        protected AttendanceSubmissionService $submissions,
        protected ScholarshipHolderService $scholarshipHolderService
    ) {
        $this->middleware('auth');
    }

    /**
     * Diário de frequência (por mês)
     */
    public function index(Request $request, AttendanceRecordDataTable $dataTable)
    {
        $user = Auth::user();
        $context = $this->scholarshipHolderService->attendanceContext(
            $user,
            $request->integer('project_id') ?: null
        );

        $holder = $context['holder'];
        $activeProjectId = $context['activeProjectId'];

        $monthString = $request->get('month', now()->format('Y-m'));
        [$year, $monthNumber] = explode('-', $monthString);

        $year = (int) $year;
        $monthNumber = (int) $monthNumber;

        $attendanceService = app(AttendanceService::class);
        $total = $attendanceService->getMonthlyTotal($holder, $year, $monthNumber, $activeProjectId);
        $limit = $attendanceService->getMonthlyLimit($holder, $activeProjectId);

        $oldestRecord = AttendanceRecord::query()
            ->where('scholarship_holder_id', $holder->id)
            ->when($activeProjectId, fn ($query) => $query->where('project_id', $activeProjectId))
            ->orderBy('date')
            ->first();

        $currentMonth = now()->format('Y-m');
        $oldestMonth = $oldestRecord
            ? $oldestRecord->date->format('Y-m')
            : $currentMonth;

        $submission = AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holder->id)
            ->when($activeProjectId, fn ($query) => $query->where('project_id', $activeProjectId))
            ->where('year', $year)
            ->where('month', $monthNumber)
            ->latest('id')
            ->first();

        $filters = [
            'month' => $monthString,
            'status' => $request->get('status'),
            'project_id' => $activeProjectId,
        ];

        return $dataTable
            ->setMode('self')
            ->setFilters($filters)
            ->render('attendance.index', [
                ...$context,
                'month' => $monthString,
                'year' => $year,
                'monthNumber' => $monthNumber,
                'total' => $total,
                'limit' => $limit,
                'currentMonth' => $currentMonth,
                'oldestMonth' => $oldestMonth,
                'oldestRecord' => $oldestRecord,
                'submission' => $submission,
                'isClosed' => ! $this->submissions->canCreateRecord($holder, $year, $monthNumber, $activeProjectId),
            ]);
    }

    /**
     * Formulário de criação
     */
    public function create(Request $request): View
    {
        $context = $this->scholarshipHolderService->attendanceContext(
            Auth::user(),
            $request->integer('project_id') ?: null
        );

        return view('attendance.create', [
            ...$context,
            'selectedMonth' => $request->get('month', now()->format('Y-m')),
        ]);
    }

    /**
     * Armazena novo registro (rascunho)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'description' => ['nullable', 'string', 'max:500'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
        ]);

        $user = Auth::user();
        $holder = $this->scholarshipHolderService->holderOrFail($user);
        $date = \Carbon\Carbon::parse($validated['date']);

        if (! $this->submissions->canCreateRecord(
            $holder,
            $date->year,
            $date->month,
            (int) $validated['project_id']
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'Este mês já foi enviado para homologação neste projeto.',
                ]);
        }

        $this->records->create($holder, $validated);

        return redirect()
            ->route('attendance.index', [
                'project_id' => $validated['project_id'],
                'month' => $date->format('Y-m'),
            ])
            ->with('success', 'Registro criado com sucesso.');
    }

    /**
     * Visualização individual
     */
    public function show(AttendanceRecord $attendanceRecord): View
    {
        $this->authorize('view', $attendanceRecord);

        return view('attendance.show', compact('attendanceRecord'));
    }

    /**
     * Edição
     */
    public function edit(AttendanceRecord $attendanceRecord)
    {
        if (! Auth::user()->can('update', $attendanceRecord)) {
            return redirect()
                ->route('attendance.index', [
                    'project_id' => $attendanceRecord->project_id,
                    'month' => optional($attendanceRecord->date)->format('Y-m') ?? now()->format('Y-m'),
                ])
                ->with('error', $attendanceRecord->editBlockReason() ?? 'Você não pode editar este registro, prazo de 7 dias expirado.');
        }

        $context = $this->scholarshipHolderService->attendanceContext(
            Auth::user(),
            $attendanceRecord->project_id
        );

        return view('attendance.edit', [
            ...$context,
            'attendanceRecord' => $attendanceRecord,
            'selectedMonth' => optional($attendanceRecord->date)->format('Y-m') ?? now()->format('Y-m'),
        ]);
    }

    /**
     * Atualização
     */
    public function update(Request $request, AttendanceRecord $attendanceRecord)
    {
        if (! Auth::user()->can('update', $attendanceRecord)) {
            return redirect()
                ->back()
                ->with('error', $attendanceRecord->editBlockReason() ?? 'Edição não permitida.');
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'description' => ['nullable', 'string', 'max:500'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
        ]);

        $date = \Carbon\Carbon::parse($validated['date']);

        $this->records->update($attendanceRecord, $validated);

        return redirect()
            ->route('attendance.index', [
                'project_id' => $validated['project_id'],
                'month' => $date->format('Y-m'),
            ])
            ->with('success', 'Registro atualizado.');
    }

    /**
     * Exclusão
     */
    public function destroy(AttendanceRecord $attendanceRecord)
    {
        $this->authorize('delete', $attendanceRecord);

        $this->records->deleteAttendance($attendanceRecord);

        return redirect()
            ->route('attendance.index', [
                'project_id' => $attendanceRecord->project_id,
                'month' => optional($attendanceRecord->date)->format('Y-m') ?? now()->format('Y-m'),
            ])
            ->with('success', 'Registro removido.');
    }
}
