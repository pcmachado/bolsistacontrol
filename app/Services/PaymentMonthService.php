<?php

namespace App\Services;

use App\Models\StudentPayment;

class PaymentMonthService
{
    public function getMonths(?int $year = null): \Illuminate\Support\Collection
    {
        $year = $year ?? now()->year;

        return collect(range(1, 12))->map(function ($month) use ($year) {

            $payments = StudentPayment::query()->where('month', $month)
                ->where('year', $year);

            $hasAny = (clone $payments)->exists();
            $allPaid = (clone $payments)->where('status', 'paid')->count() === $payments->count();

            return [
                'month' => $month,
                'year' => $year,
                'hasData' => $hasAny,
                'status' => $hasAny
                    ? ($allPaid ? 'paid' : 'pending')
                    : 'empty',
            ];
        });
    }
}
