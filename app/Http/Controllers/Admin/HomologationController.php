<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Unit;
use App\Models\ScholarshipHolder;
use App\Services\HomologationService;
use App\DataTables\HomologationsDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomologationController extends Controller
{

    protected $homologationService;

    public function __construct(HomologationService $homologationService)
    {
        $this->homologationService = $homologationService;
        $this->middleware('auth');
    }

    public function index(HomologationsDataTable $dataTable, Request $request)
    {
        // Captura filtros enviados pela view
        $filters = $request->only([
            'unit_id',
            'scholarship_holder_id',
            'status',
            'start_date',
            'end_date',
            'month',
        ]);

        // Dados auxiliares para os selects
        $units = Unit::orderBy('name')->get();
        $scholarshipHolders = ScholarshipHolder::with('user')->orderBy('id')->get();

        // Passa filtros para o DataTable e renderiza a view
        return $dataTable
            ->setFilters($filters)
            ->render('admin.homologations.index', compact('units', 'scholarshipHolders'));
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action'   => 'required|in:approve,reject',
            'records'  => 'required|array',
            'records.*'=> 'integer|exists:attendance_records,id',
            'reason'   => 'required_if:action,reject|string',
        ]);

        $records = AttendanceRecord::whereIn('id', $request->records)->get();

        // Contadores
        $processed = 0;
        $skipped   = 0;

        foreach ($records as $record) {
            if ($request->action === 'approve' && \Gate::allows('approve', $record)) {
                $this->homologationService->approve($record, auth()->id());
                $processed++;
            } elseif ($request->action === 'reject' && \Gate::allows('reject', $record)) {
                $this->homologationService->reject($record, auth()->id(), $request->reason);
                $processed++;
            } else {
                $skipped++;
            }
        }

        return response()->json([
            'success'   => true,
            'action'    => $request->action,
            'requested' => count($request->records),
            'processed' => $processed,
            'skipped'   => $skipped,
            'message'   => "Processados {$processed} registros. Ignorados {$skipped} por falta de permissão."
        ]);
    }

    public function approve(AttendanceRecord $record)
    {
        $this->authorize('approve', $record);

        $this->homologationService->approve($record, auth()->id());

        return back()->with('success', 'Registro aprovado com sucesso!');
    }

    public function reject(Request $request, AttendanceRecord $record)
    {
        $this->authorize('reject', $record);

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $this->homologationService->reject($record, auth()->id(), $request->reason);

        return back()->with('success', 'Registro rejeitado com sucesso!');
    }
}
