<?php

namespace App\Services;

use App\Models\AttendanceSubmission;
use App\Models\User;
use App\Models\AttendanceRecord;

class ScholarshipHolderDashboardService
{
    public function data(User $user): array
    {
        $holder = $user->scholarshipHolder;
        abort_if(! $holder, 403);

        $project = $holder->projects()->first();

        // notificações do usuário
        $notificacoesPendentes = $user->unreadNotifications()->count();
        $recentNotifications = $user->notifications()
            ->latest()
            ->take(5)
            ->get();

        // consulta base 
        $query = AttendanceSubmission::where('scholarship_holder_id', $holder->id);

        $now = now();

        $recordsCount = AttendanceRecord::query()
            ->where('scholarship_holder_id', $holder->id)
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->count();

        $submission = AttendanceSubmission::query()
            ->where('scholarship_holder_id', $holder->id)
            ->where('year', $now->year)
            ->where('month', $now->month)
            ->latest()
            ->first();

            $lastSubmissions = (clone $query)
                ->with('scholarshipHolder.user')
                ->where('status', AttendanceSubmission::STATUS_SUBMITTED)
                ->latest('submitted_at')
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->take(5)
                ->get();
            $lastApprovals = (clone $query)
                ->with('scholarshipHolder.user')
                ->where('status', AttendanceSubmission::STATUS_APPROVED)
                ->latest('approved_at')
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->take(5)
                ->get();

            $submissionCounts = [
                'approved' => AttendanceSubmission::query()
                    ->where('scholarship_holder_id', $holder->id)
                    ->where('status', AttendanceSubmission::STATUS_APPROVED)
                    ->count(),

                'submitted' => AttendanceSubmission::query()
                    ->where('scholarship_holder_id', $holder->id)
                    ->where('status', AttendanceSubmission::STATUS_SUBMITTED)
                    ->count(),

                'rejected' => AttendanceSubmission::query()
                    ->where('scholarship_holder_id', $holder->id)
                    ->where('status', AttendanceSubmission::STATUS_REJECTED)
                    ->count(),

                'late' => 0, // deixamos preparado (regra vem depois)
            ];

        return [
            'recordsCount' => $recordsCount,
            'submission'   => $submission,
            'status'       => $submission?->status ?? 'open',
            'user'         => $user,
            'currentYear'  => $now->year,
            'currentMonth' => $now->month,
            'scholarshipHolder' => $holder,
            'project'      => $project,
            'canCreateRecord' => ! $submission || $submission->status === AttendanceSubmission::STATUS_DRAFT,
            'submissionCounts'       => $submissionCounts,
            'lastSubmissions' => $lastSubmissions,
            'lastApprovals' => $lastApprovals,
            'notificacoesPendentes' => $notificacoesPendentes,
            'recentNotifications' => $recentNotifications,
        ];
    }
}
