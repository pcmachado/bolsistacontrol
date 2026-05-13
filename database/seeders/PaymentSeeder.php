<?php

namespace Database\Seeders;

use App\Models\AttendanceSubmission;
use App\Models\Payment;
use App\Models\ScholarshipHolder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $submissions = AttendanceSubmission::with(['scholarshipHolder.unit', 'project'])
            ->whereIn('status', [AttendanceSubmission::STATUS_APPROVED, AttendanceSubmission::STATUS_DRAFT])
            ->get();
        $admin = User::role('admin')->first();

        if ($submissions->isEmpty()) {
            $this->command?->warn('Nenhuma submissão de frequência encontrada.');

            return;
        }

        foreach ($submissions as $submission) {
            $holder = $submission->scholarshipHolder;
            $isCurrentMonth = (int) $submission->month === (int) now()->month
                && (int) $submission->year === (int) now()->year;

            $payment = Payment::updateOrCreate(
                [
                    'attendance_submission_id' => $submission->id,
                ],
                [
                    'payable_type' => ScholarshipHolder::class,
                    'payable_id' => $holder->id,
                    'scholarship_holder_id' => $holder->id,
                    'project_id' => $submission->project_id,
                    'unit_id' => $holder->unit_id,
                    'month' => $submission->month,
                    'year' => $submission->year,
                    'total_hours' => $submission->total_hours ?? 0,
                    'amount' => $submission->calculated_value ?? 0,
                    'status' => $isCurrentMonth ? Payment::STATUS_DRAFT : Payment::STATUS_CONFIRMED,
                    'sent_at' => $isCurrentMonth ? null : Carbon::create($submission->year, $submission->month, 1)->endOfMonth(),
                    'paid_at' => $isCurrentMonth ? null : Carbon::create($submission->year, $submission->month, 1)->endOfMonth()->addDays(3),
                    'confirmed_at' => $isCurrentMonth ? null : Carbon::create($submission->year, $submission->month, 1)->endOfMonth()->addDays(5),
                    'paid_by_user_id' => $isCurrentMonth ? null : $admin?->id,
                    'notes' => $isCurrentMonth
                        ? 'Pagamento do mês atual mantido em rascunho para testes.'
                        : 'Pagamento fechado pelo seeder.',
                ]
            );

            if (! $isCurrentMonth && ! $payment->receipt_number) {
                $payment->update([
                    'receipt_number' => Payment::generateReceiptNumber(),
                    'receipt_generated_at' => Carbon::create($submission->year, $submission->month, 1)->endOfMonth()->addDays(5),
                    'receipt_hash' => Payment::generateReceiptHash($payment),
                ]);
            }
        }

        $this->command?->info('Pagamentos de teste gerados com sucesso.');
    }
}
