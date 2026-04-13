<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\DataTables\AttendanceSubmissionDataTable;
use App\Services\AttendanceSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyAttendanceSubmissionController extends Controller
{
    public function __construct(
        protected AttendanceSubmissionService $service
    ) {
        $this->middleware('auth');
    }

    public function index(
        Request $request,
        AttendanceSubmissionDataTable $dataTable
    ) {

        $filters = $request->only(['status', 'month']);

        return $dataTable
            ->setMode('self')
            ->setFilters($filters)
            ->render('attendance.submissions.my');
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $submission = $this->service->createFromMonth(
            Auth::user(),
            $request->month
        );

        return redirect()
            ->route('attendance.submissions.show', $submission)
            ->with('success', 'Submissão criada com sucesso.');
    }

    public function show($id)
    {
        $submission = $this->service->findById($id);

        $this->authorize('view', $submission);

        $submission->load([
            'records',
            'scholarshipHolder.user',
            'scholarshipHolder.unit',
        ]);

        return view('attendance.submissions.show', compact('submission'));
    }
}
