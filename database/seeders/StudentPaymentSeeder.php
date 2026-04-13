<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentRecord;
use App\Models\StudentPayment;
use Carbon\Carbon;

class StudentPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $records = StudentRecord::with('student')->get();

        foreach ($records as $record) {

            $month = $record->created_at->month;
            $year  = $record->created_at->year;

            StudentPayment::updateOrCreate(
                [
                    'student_id' => $record->student_id,
                    'class_offering_id' => $record->class_offering_id,
                    'month' => $month,
                    'year'  => $year,
                ],
                [
                    'amount' => $record->total_amount,
                    'status' => collect([
                        'pending',
                        'sent',
                        'paid'
                    ])->random(),

                    'sent_at' => rand(0,1)
                        ? now()->subDays(rand(1,10))
                        : null,

                    'paid_at' => rand(0,1)
                        ? now()->subDays(rand(1,5))
                        : null,
                ]
            );
        }

        echo "✔ StudentPayments gerados com sucesso\n";
    }
}