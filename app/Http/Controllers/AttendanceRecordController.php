<?php

namespace App\Http\Controllers;

use App\DataTables\AttendanceRecordDataTable;
use App\Models\AttendanceRecord;
use App\Services\AttendanceRecordService;
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
        protected ScholarshipHolderService $holders
    ) {
        $this->middleware('auth');
    }

    /**
     * Lista de registros
     */
    public function index(
        Request $request,
        AttendanceRecordDataTable $dataTable
    ) {
        $dataTable->mode = $request->routeIs('attendance.index')
            ? 'my'
            : 'default';

        return $dataTable
            ->setFilters(
                $request->only([
                    'month',
                    'year',
                    'start_date',
                    'end_date',
                ])
            )
            ->render('attendance.index');
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
        $holder = $this->holders->holderOrFail($user);

        $date = \Carbon\Carbon::parse($validated['date']);

        // 🔒 mês fechado?
        if (! $this->submissions->canCreateRecord(
            $holder,
            $date->year,
            $date->month
        )) {
            return back()->withErrors(
                'Este mês já foi enviado para homologação.'
            );
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
    public function update(
        Request $request,
        AttendanceRecord $attendanceRecord
    ) {
        $this->authorize('update', $attendanceRecord);

        $validated = $request->validate([
            'date'        => ['required', 'date'],
            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $this->records->update(
            $attendanceRecord,
            $validated
        );

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
