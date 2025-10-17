<?php

namespace App\Http\Controllers;

use App\DataTables\AttendanceRecordDataTable;
use App\Http\Requests\AttendanceRecordStoreRequest;
use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder;
use App\Services\AttendanceRecordService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
use App\Notifications\PendingShipment;
use App\Notifications\RejectedAttendanceNotification;
use App\Events\ActivitySent;
use App\Events\RejectedAttendance;
use App\Models\User;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AttendanceRecordController extends Controller
{
    protected $attendanceRecordService;

    public function __construct(AttendanceRecordService $attendanceRecordService)
    {
        $this->attendanceRecordService = $attendanceRecordService;
        $this->middleware('auth');
    }

    public function index(Request $request, AttendanceRecordDataTable $dataTable)
    {
        if ($request->routeIs('attendance.my')) {
            $dataTable->mode = 'my';
        } else if ($request->routeIs('admin.homologations.index')) {
            $dataTable->mode = 'homologation';
        } else if ($request->routeIs('admin.attendance_records.index')) {
            $dataTable->mode = 'default';
        }
        // Aplica filtros da requisição
        $filters = $request->only([
            'project_id', 'unit_id', 'role', 'scholarship_holder_id', 'month', 'start_date', 'end_date'
        ]);

        $projects = Project::all();
        $units = Unit::all();
        $scholarship_holders = ScholarshipHolder::with('user')->get();

        return $dataTable->setFilters($filters)->render('attendance.index', compact('projects', 'units', 'scholarship_holders'));
    }

    /**
     * Exibe o formulário para registro de frequência.
     * Apenas para bolsistas.
     */
    public function create(): View
    {
        return view('attendance.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'        => 'required|date',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'nullable|date_format:H:i|after:start_time',
            'status'      => 'required|in:draft,submitted,approved,rejected',
            'observation' => 'nullable|string|max:1000',
        ]);

        try {
            $scholarshipHolder = Auth::user()->scholarshipHolder;

            if (!$scholarshipHolder) {
                return back()->with('error', 'Usuário logado não está associado a um bolsista.');
            }

            $this->attendanceRecordService->create( $validated);

            return redirect()->route('attendance.my')->with('success', 'Registro de frequência criado com sucesso!');

        } catch (\Exception $e) {
            // Se o Service lançar uma exceção de limite semanal excedido, por exemplo.
            return back()->with('error', 'Erro ao registrar frequência: ' . $e->getMessage())->withInput();
        }
    }

    public function show(AttendanceRecord $attendanceRecord): View
    {
        $attendanceRecord->load('scholarshipHolder.user', 'scholarshipHolder.unit');
        return view('attendance.show', compact('attendanceRecord'));
    }

    public function edit(AttendanceRecord $attendanceRecord): View
    {
        $this->authorize('update', $attendanceRecord);
        return view('attendance.edit', compact('attendanceRecord'));
    }

    public function update(Request $request, AttendanceRecord $attendanceRecord)
    {
        $this->authorize('update', $attendanceRecord);

        $validated = $request->validate([
            'date'        => 'required|date',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'nullable|date_format:H:i|after:start_time',
            'status'      => 'required|in:draft,submitted',
            'observation' => 'nullable|string|max:1000',
        ]);

        $this->attendanceRecordService->update($attendanceRecord, $validated);

        return redirect()->route('attendance.index')->with('success', 'Registro atualizado com sucesso!');
    }

    public function destroy(AttendanceRecord $attendanceRecord)
    {
        $this->authorize('delete', $attendanceRecord);

        $this->attendanceRecordService->delete($attendanceRecord);

        return redirect()->route('attendance.index')->with('success', 'Registro excluído com sucesso!');
    }

    public function submit(AttendanceRecord $record)
    {
        $this->authorize('submit', $record);
        $this->attendanceRecordService->submitRecord($record);
        return back()->with('success', 'Registro enviado para homologação.');
    }

    public function approve(AttendanceRecord $record)
    {
        $this->authorize('approve', $record);
        $this->attendanceRecordService->approveRecord($record);
        return back()->with('success', 'Registro aprovado.');
    }

    public function reject(Request $request, AttendanceRecord $record)
    {
        $this->authorize('reject', $record);
        $this->attendanceRecordService->rejectRecord($record, $request->input('reason'));
        return back()->with('success', 'Registro recusado.');
    }

    /*public function pending(): View
    {
        $scholarshipHolder = Auth::user()->scholarshipHolder;

        if (!$scholarshipHolder) {
            abort(403, 'Usuário não é bolsista.');
        }

        $pendingRecords = AttendanceRecord::where('scholarship_holder_id', $scholarshipHolder->id)
            ->where('status', AttendanceRecord::STATUS_PENDING) // ajuste conforme sua constante
            ->orderBy('date', 'desc')
            ->get();

        return view('attendance.pending', compact('pendingRecords'));
    }*/

    public function report(Request $request)
    {
        $unitId = Auth::user()->unit_id;
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $report = $this->attendanceRecordService->generateReport($unitId, $month, $year);

        return view('attendance.report', compact('report', 'month', 'year'));
    }

    public function approved(AttendanceRecordDataTable $dataTable)
    {
        $dataTable->mode = 'approved';
        return $dataTable->render('attendance.card.approved');
    }

    public function pending(AttendanceRecordDataTable $dataTable)
    {
        $dataTable->mode = 'pending';
        return $dataTable->render('attendance.card.pending');
    }

    public function rejected(AttendanceRecordDataTable $dataTable)
    {
        $dataTable->mode = 'rejected';
        return $dataTable->render('attendance.card.rejected');
    }

    public function late(AttendanceRecordDataTable $dataTable)
    {
        $dataTable->mode = 'late'; // novo modo
        return $dataTable->render('attendance.card.late');
    }

}
