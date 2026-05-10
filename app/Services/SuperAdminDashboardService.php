<?php

namespace App\Services;

use App\Models\AttendanceSubmission;
use App\Models\Institution;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\User;

class SuperAdminDashboardService
{
    public function getStats(): array
    {
        return [

            'institutions' => Institution::count(),

            'projects' => Project::count(),

            'active_projects' => Project::where('status', 'active')->count(),

            'users' => User::count(),

            'scholarship_holders' =>
                ScholarshipHolder::count(),

            'active_holders' =>
                ScholarshipHolder::where(
                    'status',
                    'active'
                )->count(),

            'pending_submissions' =>
                AttendanceSubmission::where(
                    'status',
                    AttendanceSubmission::STATUS_SUBMITTED
                )->count(),

            'pending_payments' =>
                Payment::where(
                    'status',
                    Payment::STATUS_SENT
                )->count(),
        ];
    }

    public function institutions()
    {
        return Institution::query()

            ->withCount([
                'projects',
                'users',
                'units',
            ])

            ->latest()

            ->get();
    }

    public function recentUsers()
    {
        return User::query()

            ->with([
                'roles',
                'unit',
            ])

            ->latest()

            ->limit(10)

            ->get();
    }

    public function paymentOverview(): array
    {
        return [

            'pending' => Payment::where(
                'status',
                Payment::STATUS_PENDING
            )->sum('amount'),

            'paid' => Payment::where(
                'status',
                Payment::STATUS_PAID
            )->sum('amount'),

            'confirmed' => Payment::where(
                'status',
                Payment::STATUS_CONFIRMED
            )->sum('amount'),
        ];
    }

    public function academicRisk()
    {
        return ScholarshipHolder::query()

            ->whereHas('studentRecords', function ($query) {

                $query->where('absence_percent', '>', 15);

            })

            ->with([
                'user',
                'projects',
            ])

            ->limit(20)

            ->get();
    }
}