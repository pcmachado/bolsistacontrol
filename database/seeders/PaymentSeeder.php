<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AttendanceSubmission;
use App\Models\Payment;
use App\Models\ScholarshipHolder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $holders = ScholarshipHolder::with(['projects', 'unit'])->get();
        $admin   = User::role('admin')->first();
        $submission = AttendanceSubmission::where('status', AttendanceSubmission::STATUS_APPROVED)->first();

        if ($holders->isEmpty()) {
            $this->command->warn('Nenhum bolsista encontrado.');
            return;
        }

        foreach ($holders as $holder) {
            // últimos 6 meses
            for ($i = 0; $i < 6; $i++) {
                $date  = now()->subMonths($i);
                $month = (int) $date->format('m');
                $year  = (int) $date->format('Y');

                // evita duplicidade
                if (
                    Payment::where('payable_type', ScholarshipHolder::class)
                        ->where('payable_id', $holder->id)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->exists()
                ) {
                    continue;
                }

                $status = Arr::random([
                    Payment::STATUS_SENT,
                    Payment::STATUS_PAID,
                    Payment::STATUS_CONFIRMED,
                ]);

                $payment = Payment::create([
                    // 🔹 polymorphic
                    'payable_type'          => ScholarshipHolder::class,
                    'payable_id'            => $holder->id,

                    'scholarship_holder_id' => $holder->id,
                    'project_id'            => optional($holder->projects->first())->id,
                    'unit_id'               => $holder->unit_id,

                    'month'                 => $month,
                    'year'                  => $year,
                    'total_hours'           => $submission->total_hours,
                    'amount'                => $submission->calculated_value,
                    'status'                => $status,
                    'sent_at'               => now()->subDays(rand(5, 20)),
                ]);

                // ajustes conforme status
                if ($status === Payment::STATUS_PAID) {
                    $payment->update([
                        'paid_at' => now()->subDays(rand(1, 5)),
                        'paid_by_user_id' => $admin?->id,
                    ]);
                }

                if ($status === Payment::STATUS_CONFIRMED) {
                    $payment->update([
                        'paid_at'       => now()->subDays(rand(3, 7)),
                        'confirmed_at'  => now()->subDays(rand(1, 3)),
                        'paid_by_user_id' => $admin?->id,
                        'receipt_number' => Payment::generateReceiptNumber(),
                        'receipt_generated_at' => Carbon::now()->subDays(rand(1, 3)),
                        'receipt_hash' => Payment::generateReceiptHash($payment),
                    ]);
                }
            }
        }

        $this->command->info('Pagamentos de teste gerados com sucesso.');
    }
}

