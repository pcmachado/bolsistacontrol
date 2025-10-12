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
        if ($request->routeIs('admin.homologations.index')) {
            $dataTable->mode = 'homologation';
        } else {
            $dataTable->mode = 'default';
        }

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

    public function store(AttendanceRecordStoreRequest $request)
    {
        try {
            // O AttendanceRecordStoreRequest já validou os dados, incluindo a data e horas.
            $data = $request->validated();
            
            // Pega o ID do bolsista logado
            $scholarshipHolder = Auth::user()->scholarshipHolder;

            if (!$scholarshipHolder) {
                return back()->with('error', 'Usuário logado não está associado a um bolsista.');
            }

            // O service se encarrega do cálculo do tempo total e da validação do limite semanal
            $this->attendanceRecordService->createRecord($scholarshipHolder->id, $data);

            return redirect()->route('attendance.index')->with('success', 'Registro de frequência criado com sucesso!');

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

    public function update(AttendanceRecordStoreRequest $request, AttendanceRecord $attendanceRecord)
    {
        $this->authorize('update', $attendanceRecord);

        $this->attendanceRecordService->updateRecord($attendanceRecord, $request->validated());

        return redirect()->route('attendance.index')->with('success', 'Registro atualizado com sucesso!');
    }

    public function destroy(AttendanceRecord $attendanceRecord)
    {
        $this->authorize('delete', $attendanceRecord);

        $this->attendanceRecordService->deleteRecord($attendanceRecord);

        return redirect()->route('attendance.index')->with('success', 'Registro excluído com sucesso!');
    }

    public function submit(AttendanceRecord $record, AttendanceRecordService $service)
    {
        $this->authorize('submit', $record);
        $service->submitRecord($record);
        return back()->with('success', 'Registro enviado para homologação.');
    }

    public function approve(AttendanceRecord $record, AttendanceRecordService $service)
    {
        $this->authorize('approve', $record);
        $service->approveRecord($record);
        return back()->with('success', 'Registro aprovado.');
    }

    public function reject(Request $request, AttendanceRecord $record, AttendanceRecordService $service)
    {
        $this->authorize('reject', $record);
        $service->rejectRecord($record, $request->input('reason'));
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

    public function report(Request $request, AttendanceRecordService $service)
    {
        $unitId = Auth::user()->unit_id;
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $report = $service->generateReport($unitId, $month, $year);

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
