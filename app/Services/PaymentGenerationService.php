<?php

namespace App\Services;

use App\Models\AttendanceSubmission;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentGenerationService
{
    public function generateFromSubmission(AttendanceSubmission $submission): Payment
    {
        if (! $submission->isApproved()) {
            throw new \DomainException('Submissão não aprovada.');
        }

        // 🔒 evita duplicidade
        $existing = Payment::query()->where('attendance_submission_id', $submission->id)->first();

        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($submission) {

            $holder = $submission->scholarshipHolder;
            $project = $submission->relationLoaded('project')
                ? $submission->project
                : $submission->project()->with('positions')->first();
            $rate = $project?->hourlyRateForScholarshipHolder($holder) ?? 0;

            return Payment::create([
                'attendance_submission_id' => $submission->id,

                'scholarship_holder_id' => $holder->id,
                'project_id' => $project?->id ?? $submission->project_id,
                'unit_id' => $holder->unit_id,

                'month' => $submission->month,
                'year' => $submission->year,

                'amount' => $submission->total_hours * $rate,

                'status' => Payment::STATUS_SENT,
                'sent_at' => now(),
            ]);
        });
    }
}
