<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\HomologationsDataTable;
use App\Http\Controllers\Controller;
use App\Models\AttendanceSubmission;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\Unit;
use App\Services\HomologationService;
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

        $projects = Project::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $user->visibleProjectIds())
            ->orderBy('name')
            ->get();

        $units = Unit::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $user->visibleUnitIds())
            ->orderBy('name')
            ->get();

        $scholarship_holders = ScholarshipHolder::query()
            ->with('user')
            ->when($user->visibleUnitIds()->isNotEmpty(), fn ($query) => $query->whereIn('unit_id', $user->visibleUnitIds())
            )
            ->orderBy('name')
            ->get();

        return $dataTable
            ->setFilters($filters)
            ->render('admin.homologations.index', compact('projects', 'units', 'scholarship_holders'));
    }

    public function show(AttendanceSubmission $submission)
    {
        $this->authorize('view', $submission);

        return view('admin.homologations.show', compact('submission'));
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'submissions' => 'required|array',
            'submissions.*' => 'integer|exists:attendance_submissions,id',
            'reason' => 'required_if:action,reject|string|max:1000',
        ]);

        $submissions = AttendanceSubmission::whereIn('id', $request->submissions)->get();

        $processed = 0;
        $skipped = 0;

        foreach ($submissions as $submission) {
            if (! Gate::allows('approve', $submission)) {
                $skipped++;

                continue;
            }

            if ($request->action === 'approve') {
                $this->homologationService->approve($submission, Auth::id());
            }

            if ($request->action === 'reject') {
                $this->homologationService->reject($submission, Auth::id(), $request->reason);
            }

            $processed++;
        }

        return response()->json([
            'success' => true,
            'action' => $request->action,
            'requested' => count($request->submissions),
            'processed' => $processed,
            'skipped' => $skipped,
            'message' => "Processadas {$processed} submissoes. {$skipped} ignoradas.",
        ]);
    }

    public function approve(AttendanceSubmission $submission)
    {
        $this->authorize('approve', $submission);

        $this->homologationService->approve($submission, Auth::id());

        return back()->with('success', 'Submissao homologada com sucesso.');
    }

    public function reject(Request $request, AttendanceSubmission $submission)
    {
        $this->authorize('reject', $submission);

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $this->homologationService->reject($submission, Auth::id(), $request->reason);

        return back()->with('success', 'Submissao rejeitada com sucesso.');
    }

    public function late(AttendanceSubmission $submission)
    {
        $this->homologationService->markAsLate($submission, Auth::id());

        return back()->with('success', 'Submissao marcada como atrasada com sucesso.');
    }
}
