<?php

namespace App\Services;

use App\Models\ClassOffering;
use App\Models\ClassOfferingSubmission;
use App\Models\StudentRecord;
use App\Models\StudentPayment;
use Illuminate\Support\Facades\DB;

class ClassOfferingPaymentService
{
    public function generate(ClassOffering $class, int $month, int $year)
    {
        $submission = ClassOfferingSubmission::where([
            'class_offering_id' => $class->id,
            'month' => $month,
            'year'  => $year,
        ])->firstOrFail();

        if ($submission->status !== 'submitted') {
            throw new \DomainException('A submissão não está pronta para pagamento.');
        }

        $records = StudentRecord::where('class_offering_id', $class->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        if ($records->isEmpty()) {
            throw new \DomainException('Nenhum lançamento encontrado.');
        }

        DB::transaction(function () use ($records, $class, $month, $year) {

            foreach ($records as $record) {

                StudentPayment::updateOrCreate(
                    [
                        'student_id' => $record->student_id,
                        'class_offering_id' => $class->id,
                        'month' => $month,
                        'year'  => $year,
                    ],
                    [
                        'amount' => $record->total_amount,
                        'status' => StudentPayment::STATUS_PENDING,
                    ]
                );
            }
        });

        return true;
    }

    public function reject(ClassOfferingSubmission $submission, string $reason, int $userId)
    {
        DB::transaction(function () use ($submission, $reason, $userId) {

            $submission->update([
                'status' => 'rejected',
                'rejected_reason' => $reason,
                'rejected_at' => now(),
            ]);

            // 🔥 remove pagamentos gerados
            StudentPayment::where([
                'class_offering_id' => $submission->class_offering_id,
                'month' => $submission->month,
                'year'  => $submission->year,
            ])->delete();
        });
    }
}