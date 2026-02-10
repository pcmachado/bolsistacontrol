<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceReportController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $holder = $user->scholarshipHolder;

        abort_if(! $holder, 403);

        $submissions = AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holder->id)
            ->whereIn('status', [
                AttendanceSubmission::STATUS_APPROVED,
                AttendanceSubmission::STATUS_SUBMITTED,
            ])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('attendance.reports.index',compact('submissions'));
    }

    public function monthly(AttendanceSubmission $submission)
    {
        $this->authorize('report', $submission);

        $submission->load([
            'records',
            'scholarshipHolder.user',
            'scholarshipHolder.unit',
            'scholarshipHolder.projects',
        ]);

        return view('attendance.reports.monthly',compact('submission'));
    }
}
