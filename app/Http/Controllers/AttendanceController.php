<?php

namespace App\Http\Controllers;

use App\DataTables\AttendanceDataTable;
use App\Http\Requests\AttendanceRecordStoreRequest;
use App\Models\AttendanceRecord;
use App\Models\ScholarshipHolder;
use App\Services\AttendanceService;
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

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
        $this->middleware('auth');
    }

    public function index(Request $request, AttendanceDataTable $dataTable)
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
        return view('attendance.create_edit');
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
            $this->attendanceService->createRecord($scholarshipHolder->id, $data);

            return redirect()->route('attendance.index')->with('success', 'Registro de frequência criado com sucesso!');

        } catch (\Exception $e) {
            // Se o Service lançar uma exceção de limite semanal excedido, por exemplo.
            return back()->with('error', 'Erro ao registrar frequência: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Envia todos os registros de um mês para homologação (Ação do Bolsista).
     */
    public function sendForApproval(Request $request, $month, $year)
    {
        // Garante que a requisição seja feita pelo Bolsista
        $scholarshipHolder = Auth::user()->scholarshipHolder;
        if (!$scholarshipHolder) {
            return back()->with('error', 'Você não tem permissão para esta ação.');
        }

        try {
            // O service se encarrega de: 
            // 1. Mudar o status de todos os DRAFTs do mês para PENDING.
            // 2. Criar a notificação para o Coordenador Adjunto.
            $this->attendanceService->submitMonthlyReport($scholarshipHolder, $month, $year);

            Notification::send($scholarshipHolder->coordinator, new PendingShipment($scholarshipHolder));
            event(new ActivitySent($scholarshipHolder));

            return redirect()->route('attendance.index')->with('success', 'Relatório mensal enviado para homologação.');

        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao enviar relatório: ' . $e->getMessage());
        }
    }

    // --- AÇÕES DO COORDENADOR ADJUNTO ---

    /**
     * Homologa um ou mais registros de frequência (Ação do Coordenador Adjunto).
     */
    public function approve(Request $request)
    {
        $request->validate([
            'record_ids' => 'required|array',
            'record_ids.*' => 'exists:attendance_records,id',
        ]);
        
        $user = Auth::user();
        // TODO: Adicionar checagem de role/permissão para Coordenador Adjunto
        if (!$user->hasRole('coordenador_adjunto')) { 
            return back()->with('error', 'Acesso negado. Apenas Coordenadores Adjuntos podem homologar.');
        }

        try {
            $this->attendanceService->updateStatus($request->record_ids, AttendanceRecord::STATUS_APPROVED, $user->id);
            return redirect()->route('attendance.homologation')->with('success', 'Registros homologados com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Falha na homologação: ' . $e->getMessage());
        }
    }

    /**
     * Rejeita um ou mais registros de frequência (Ação do Coordenador Adjunto).
     */
    public function reject(Request $request)
    {
        $request->validate([
            'record_ids' => 'required|array',
            'record_ids.*' => 'exists:attendance_records,id',
            'rejection_reason' => 'required|string|max:500', // Necessário motivo da rejeição
        ]);

        $user = Auth::user();
        // TODO: Adicionar checagem de role/permissão para Coordenador Adjunto
        if (!$user->hasRole('coordenador_adjunto')) {
            return back()->with('error', 'Acesso negado. Apenas Coordenadores Adjuntos podem rejeitar.');
        }

        try {
            $this->attendanceService->updateStatus(
                $request->record_ids, 
                AttendanceRecord::STATUS_REJECTED, 
                $user->id, 
                $request->rejection_reason
            );

            foreach ($request->record_ids as $id) {
                $record = AttendanceRecord::find($id);
                Notification::send($record->scholarshipHolder->user, new RejectedAttendanceNotification($record));
                event(new RejectedAttendance($record));
            }

            return redirect()->route('attendance.homologation')->with('success', 'Registros rejeitados e notificados ao bolsista.');
        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao rejeitar registros: ' . $e->getMessage());
        }
    }

    /**
     * Exibe a view de homologação para o Coordenador (Registros PENDENTES).
     */
    public function homologation(AttendanceDataTable $dataTable)
    {
        // TODO: Adicionar checagem de role/permissão para Coordenador Adjunto
        if (!Auth::user()->hasRole('coordenador_adjunto')) {
            return redirect()->route('home')->with('error', 'Acesso negado.');
        }

        // Passa o modo 'homologation' para o DataTable filtrar apenas PENDINGs
        return $dataTable->setMode('homologation')->render('attendance.homologation_dashboard');
    }
}
