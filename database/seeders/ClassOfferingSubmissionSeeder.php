<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassOffering;
use App\Models\ClassOfferingSubmission;
use App\Models\StudentRecord;
use Carbon\Carbon;

class ClassOfferingSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $start = Carbon::create(2026, 1, 1);
        $end   = now()->startOfMonth();

        $classes = ClassOffering::all();

        foreach ($classes as $class) {

            $cursor = $start->copy();

            while ($cursor <= $end) {

                $month = $cursor->month;
                $year  = $cursor->year;

                // 📊 registros da turma naquele mês
                $records = StudentRecord::where('class_offering_id', $class->id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->get();

                if ($records->isEmpty()) {
                    $cursor->addMonth();
                    continue;
                }

                // 🎯 status automático
                if ($cursor->isSameMonth(now())) {
                    $status = 'draft';
                } elseif ($cursor->diffInMonths(now()) === 1) {
                    $status = 'submitted';
                } else {
                    $status = collect(['approved', 'rejected'])->random();
                }

                ClassOfferingSubmission::updateOrCreate(
                    [
                        'class_offering_id' => $class->id,
                        'month' => $month,
                        'year'  => $year,
                    ],
                    [
                        'total_students' => $records->count(),
                        'total_amount'   => $records->sum('total_amount'),
                        'status'         => $status,

                        'submitted_at' => in_array($status, ['submitted','approved','rejected'])
                            ? $cursor->copy()->endOfMonth()
                            : null,

                        'approved_at' => $status === 'approved'
                            ? $cursor->copy()->endOfMonth()->addDays(2)
                            : null,

                        'rejected_at' => $status === 'rejected'
                            ? $cursor->copy()->endOfMonth()->addDays(2)
                            : null,

                        'rejected_reason' => $status === 'rejected'
                            ? 'Inconsistência nos lançamentos da turma'
                            : null,
                    ]
                );

                $cursor->addMonth();
            }
        }

        echo "✔ ClassOfferingSubmission gerados com sucesso!\n";
    }
}