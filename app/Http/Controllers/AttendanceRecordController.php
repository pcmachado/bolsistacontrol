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
use App\Notifications\RejectedAttendance;
use App\Notifications\ApprovedAttendance;
use App\Services\NotificationService;
use App\Events\ActivitySent;
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
            'project_id',
            'unit_id', 'role',
            'scholarship_holder_id',
            'month',
            'monthYear',
            'year',
            'start_date',
            'end_date',
            'status',
        ]);

        $projects = Project::all();
        $units = Unit::all();
        $scholarship_holders = ScholarshipHolder::with('user')->get();

        return $dataTable->setFilters($filters)->render('attendance.index', compact('projects', 'units', 'scholarship_holders'),['pagemode' => $dataTable->mode]);
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
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string|max:500',
        ]);

        $holder = auth()->user()->scholarshipHolder;

        if (!$holder) {
            abort(403, 'Apenas bolsistas podem registrar frequência.');
        }

        // Calcula horas trabalhadas
        $start = \Carbon\Carbon::parse($request->start_time);
        $end   = \Carbon\Carbon::parse($request->end_time);
        $hours = $start->diffInMinutes($end) / 60;

        $record = AttendanceRecord::create([
            'scholarship_holder_id' => $holder->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'hours' => $hours,
            'calculated_value' => $hours * ($holder->scholarship->value_per_hour ?? 0),
            'description' => $request->description,
            'status' => AttendanceRecord::STATUS_DRAFT,
        ]);

        return redirect()->route('attendance.my')->with('success', 'Registro de frequência criado como rascunho.');
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
            'description' => 'nullable|string|max:1000',
        ]);

        $this->attendanceRecordService->update($attendanceRecord, $validated);

        return redirect()->route('attendance.my')->with('success', 'Registro atualizado com sucesso!');
    }

    public function destroy(AttendanceRecord $attendanceRecord)
    {
        $this->authorize('delete', $attendanceRecord);

        $this->attendanceRecordService->delete($attendanceRecord);

        return redirect()->route('attendance.my')->with('success', 'Registro excluído com sucesso!');
    }

    public function submit(AttendanceRecord $attendanceRecord)
    {
        $this->authorize('submit', $attendanceRecord);
        $this->attendanceRecordService->submitRecord($attendanceRecord);

        app(NotificationService::class)->sendToUser(
            $attendanceRecord->scholarshipHolder->coordenador_adjunto,
            "O bolsista {$user->name} enviou um registro de frequência para análise.",
            'submitted'
        );

        return back()->with('success', 'Registro enviado para homologação.');
    }

    public function approve(AttendanceRecord $attendanceRecord)
    {
        $this->authorize('approve', $attendanceRecord);
        $this->attendanceRecordService->approveRecord($attendanceRecord);

        app(NotificationService::class)->sendToUser(
            $attendanceRecord->scholarshipHolder->user,
            "Seu registro de frequência de {$attendanceRecord->date->format('d/m/Y')} foi homologado.",
            'approved'
        );

        return back()->with('success', 'Registro aprovado.');
    }

    public function reject(Request $request, AttendanceRecord $attendanceRecord)
    {
        $this->authorize('reject', $attendanceRecord);
        $this->attendanceRecordService->rejectRecord($attendanceRecord, $request->input('reason'));

        app(NotificationService::class)->sendToUser(
            $attendanceRecord->scholarshipHolder->user,
            "Seu registro de frequência foi rejeitado. Motivo: {$attendanceRecord->rejected_reason}.",
            'rejected'
        );

        return back()->with('success', 'Registro recusado.');
    }

    public function pending(): View
    {
        $scholarshipHolder = Auth::user()->scholarshipHolder;

        if (!$scholarshipHolder) {
            abort(403, 'Usuário não é bolsista.');
        }

        $pendingRecords = AttendanceRecord::where('scholarship_holder_id', $scholarshipHolder->id)
            ->where('status', AttendanceRecord::STATUS_SUBMITTED) // ajuste conforme sua constante
            ->orderBy('date', 'desc')
            ->get();
        return view('attendance.pending', compact('pendingRecords'));
    }

    /*public function report(Request $request)
    {
        $unitId = Auth::user()->unit_id;
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $report = $this->attendanceRecordService->generateReport($unitId, $month, $year);

        return view('attendance.report', compact('report', 'month', 'year'));
    }*/

    public function approved(AttendanceRecordDataTable $dataTable)
    {
        $dataTable->mode = 'approved';
        return $dataTable->render('attendance.card.approved');
    }

    public function submitted(AttendanceRecordDataTable $dataTable)
    {
        $dataTable->mode = 'submitted';
        return $dataTable->render('attendance.card.submitted');
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

    public function submissions(Request $request)
    {
        $query = AttendanceRecord::with('scholarshipHolder.user')
        ->where('status', 'submitted');

        // 🔹 Filtro por mês/ano
        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('date', $year)->whereMonth('date', $month);
        }

        // 🔹 Filtro por unidade
        if ($request->filled('unit_id')) {
            $query->whereHas('scholarshipHolder', fn($q) =>
                $q->where('unit_id', $request->unit_id)
            );
        }

        $submissions = $query->latest('date')->paginate(15);

        $units = Unit::orderBy('name')->get();

        return view('attendance.submissions', compact('submissions', 'units'));
    }

    public function approvals(Request $request)
    {
        $query = AttendanceRecord::with('scholarshipHolder.user')
        ->where('status', 'approved');

        // 🔹 Filtro por mês/ano
        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('date', $year)->whereMonth('date', $month);
        }

        // 🔹 Filtro por unidade
        if ($request->filled('unit_id')) {
            $query->whereHas('scholarshipHolder', fn($q) =>
                $q->where('unit_id', $request->unit_id)
            );
        }

        $approvals = $query->latest('updated_at')->paginate(15);

        $units = Unit::orderBy('name')->get();

        return view('attendance.approvals', compact('approvals', 'units'));
    }

}
