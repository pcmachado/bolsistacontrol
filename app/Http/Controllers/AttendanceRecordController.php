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
        $holder = $this->scholarshipHolderService->holderOrFail($user);

        // 📅 mês atual padrão
        $monthString = $request->get('month', now()->format('Y-m'));

        [$year, $monthNumber] = explode('-', $monthString);

        $year = (int) $year;
        $monthNumber = (int) $monthNumber;

        // 📊 serviço
        $attendanceService = app(AttendanceService::class);

        $total = $attendanceService->getMonthlyTotal($holder, $year, $monthNumber);
        $limit = $attendanceService->getMonthlyLimit($holder);

        // 📅 primeiro registro
        $oldestRecord = AttendanceRecord::query()
            ->where('scholarship_holder_id', $holder->id)
            ->orderBy('date')
            ->first();

        // 📅 controle navegação
        $currentMonth = now()->format('Y-m');
        $oldestMonth = $oldestRecord
            ? $oldestRecord->date->format('Y-m')
            : $currentMonth;

        // filtros para DataTable
        $filters = [
            'month'  => $monthString,
            'status' => $request->get('status'),
        ];

        return $dataTable
            ->setMode('self')
            ->setFilters($filters)
            ->render('attendance.index', [
                'month'         => $monthString,
                'year'          => $year,
                'monthNumber'   => $monthNumber,
                'total'         => $total,
                'limit'         => $limit,
                'currentMonth'  => $currentMonth,
                'oldestMonth'   => $oldestMonth,
                'oldestRecord'  => $oldestRecord,
                'submission'    => null,
            ]);
    }

    /**
     * Formulário de criação
     */
    public function create(): View
    {
        return view('attendance.create');
    }

    /**
     * Armazena novo registro (rascunho)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'        => ['required', 'date'],
            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $user   = Auth::user();
        $holder = $this->scholarshipHolderService->holderOrFail($user);

        $date = \Carbon\Carbon::parse($validated['date']);

        // 🔒 mês fechado?
        if (! $this->submissions->canCreateRecord(
            $holder,
            $date->year,
            $date->month
        )) {
            return back()
            ->withInput()
            ->withErrors([
                'Este mês já foi enviado para homologação.'
            ]);
        }

        $this->records->create($holder, $validated);

        return redirect()
            ->route('attendance.index')
            ->with('success', 'Registro criado com sucesso.');
    }

    /**
     * Visualização individual
     */
    public function show(AttendanceRecord $attendanceRecord): View
    {
        $this->authorize('view', $attendanceRecord);

        return view(
            'attendance.show',
            compact('attendanceRecord')
        );
    }

    /**
     * Edição
     */
    public function edit(AttendanceRecord $attendanceRecord): View
    {
        $this->authorize('update', $attendanceRecord);

        return view(
            'attendance.edit',
            compact('attendanceRecord')
        );
    }

    /**
     * Atualização
     */
    public function update(Request $request, AttendanceRecord $attendanceRecord )
    {
        $this->authorize('update', $attendanceRecord);

        $validated = $request->validate([
            'date'        => ['required', 'date'],
            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $this->records->update($attendanceRecord, $validated);

        return redirect()
            ->route('attendance.index')
            ->with('success', 'Registro atualizado.');
    }

    /**
     * Exclusão
     */
    public function destroy(AttendanceRecord $attendanceRecord)
    {
        $this->authorize('delete', $attendanceRecord);

        $this->records->delete($attendanceRecord);

        return redirect()
            ->route('attendance.index')
            ->with('success', 'Registro removido.');
    }

}
