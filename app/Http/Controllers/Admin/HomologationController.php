<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSubmission;
use App\Models\Unit;
use App\Models\ScholarshipHolder;
use App\Models\Project;
use App\Services\HomologationService;
use App\DataTables\HomologationsDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class HomologationController extends Controller
{
    protected HomologationService $homologationService;

    public function __construct(HomologationService $homologationService)
    {
        $this->middleware('auth');
        $this->homologationService = $homologationService;
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAGEM
    |--------------------------------------------------------------------------
    */

    public function index(Request $request, HomologationsDataTable $dataTable)
    {
        $user = Auth::user();

        $filters = $request->only([
            'status',
            'month',
            'unit_id',
            'project_id',
            'role',
            'scholarship_holder_id',
        ]);

        if ($user->hasRole('admin')) {
            $projects = Project::query()->orderBy('name')->get();
        } elseif ($user->hasRole(['coordenador_geral', 'coordenador_adjunto_geral'])) {
            $projects = Project::query()
                ->where('institution_id', $user->institution_id)
                ->orderBy('name')
                ->get();
        } elseif ($user->hasRole('coordenador_adjunto')) {
            $unitIds = $user->units()->pluck('units.id');
            $projects = Project::query()
                ->whereHas('units', fn ($q) => $q->whereIn('units.id', $unitIds))
                ->orderBy('name')
                ->get();
        } else {
            $projects = collect();
        }

        if ($user->hasRole('admin')) {
            $units = Unit::query()->orderBy('name')->get();
        } elseif ($user->hasRole(['coordenador_geral', 'coordenador_adjunto_geral'])) {
            $units = Unit::query()
                ->where('institution_id', $user->institution_id)
                ->orderBy('name')
                ->get();
        } elseif ($user->hasRole('coordenador_adjunto')) {
            $units = $user->units()->orderBy('name')->get();
        } else {
            $units = collect();
        }

        $scholarship_holders = ScholarshipHolder::query()
            ->with('user')
            ->orderBy('name');

        if ($user->hasRole('admin')) {
            $scholarship_holders = $scholarship_holders->get();
        } elseif ($user->hasRole(['coordenador_geral', 'coordenador_adjunto_geral'])) {
            $scholarship_holders = $scholarship_holders
                ->whereHas('unit', fn ($q) => $q->where('institution_id', $user->institution_id))
                ->get();
        } elseif ($user->hasRole('coordenador_adjunto')) {
            $unitIds = $user->units()->pluck('units.id');
            $scholarship_holders = $scholarship_holders
                ->whereIn('unit_id', $unitIds)
                ->get();
        } else {
            $scholarship_holders = collect();
        }

        return $dataTable
            ->setFilters($filters)
            ->render(
                'admin.homologations.index',
                compact('projects', 'units', 'scholarship_holders')
            );
    }

    /*
    |--------------------------------------------------------------------------
    | APROVAÇÃO EM LOTE (POR SUBMISSÃO)
    |--------------------------------------------------------------------------
    */

    public function bulk(Request $request)
    {
        $request->validate([
            'action'       => 'required|in:approve,reject',
            'submissions'  => 'required|array',
            'submissions.*'=> 'integer|exists:attendance_submissions,id',
            'reason'       => 'required_if:action,reject|string|max:1000',
        ]);

        $submissions = AttendanceSubmission::whereIn('id', $request->submissions)->get();

        $processed = 0;
        $skipped   = 0;

        foreach ($submissions as $submission) {

            if (!Gate::allows('approve', $submission)) {
                $skipped++;
                continue;
            }

            if ($request->action === 'approve') {
                $this->homologationService->approve($submission, Auth::id());
            }

            if ($request->action === 'reject') {
                $this->homologationService->reject(
                    $submission,
                    Auth::id(),
                    $request->reason
                );
            }

            $processed++;
        }

        return response()->json([
            'success'   => true,
            'action'    => $request->action,
            'requested' => count($request->submissions),
            'processed' => $processed,
            'skipped'   => $skipped,
            'message'   => "Processadas {$processed} submissões. {$skipped} ignoradas."
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | APROVAR INDIVIDUAL
    |--------------------------------------------------------------------------
    */

    public function approve(AttendanceSubmission $submission)
    {
        $this->authorize('approve', $submission);

        $this->homologationService->approve($submission, Auth::id());

        return back()->with('success', 'Submissão homologada com sucesso!');
    }

    /*
    |--------------------------------------------------------------------------
    | REJEITAR INDIVIDUAL
    |--------------------------------------------------------------------------
    */

    public function reject(Request $request, AttendanceSubmission $submission)
    {
        $this->authorize('reject', $submission);

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $this->homologationService->reject(
            $submission,
            Auth::id(),
            $request->reason
        );

        return back()->with('success', 'Submissão rejeitada com sucesso!');
    }
}
