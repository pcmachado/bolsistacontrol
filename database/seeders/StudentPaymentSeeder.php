<?php

namespace Database\Seeders;

use App\Models\StudentMonthRecord;
use App\Models\StudentPayment;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $records = StudentMonthRecord::with('classOffering.project')->get();
        $payer = User::role('admin')->first();

        foreach ($records as $record) {
            $isCurrentMonth = (int) $record->month === (int) now()->month
                && (int) $record->year === (int) now()->year;
            $amount = $record->attended_classes * ($record->classOffering->project?->student_daily_rate ?? 0);

            StudentPayment::updateOrCreate(
                [
                    'student_id' => $record->student_id,
                    'class_offering_id' => $record->class_offering_id,
                    'month' => $record->month,
                    'year' => $record->year,
                ],
                [
                    'amount' => $amount,
                    'status' => $isCurrentMonth ? StudentPayment::STATUS_SENT : StudentPayment::STATUS_PAID,
                    'sent_at' => $isCurrentMonth
                        ? now()->subDays(rand(1, min(10, now()->day)))
                        : now()->setDate($record->year, $record->month, 1)->endOfMonth(),
                    'paid_at' => $isCurrentMonth
                        ? null
                        : now()->setDate($record->year, $record->month, 1)->endOfMonth()->addDays(3),
                    'paid_by' => $isCurrentMonth ? null : $payer?->id,
                    'notes' => $isCurrentMonth
                        ? 'Pagamento do mês atual mantido aberto para testes.'
                        : 'Pagamento mensal fechado pelo seeder.',
                ]
            );
        }

        $this->command?->info('StudentPayments gerados com sucesso.');
    }
}
