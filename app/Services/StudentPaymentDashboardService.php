<?php

namespace App\Services;

use App\Models\StudentPayment;

class StudentPaymentDashboardService
{
    public function data(array $filters = [])
    {
        $month = $filters['month'] ?? now()->month;
        $year  = $filters['year'] ?? now()->year;

        $query = StudentPayment::query()
            ->where('month', $month)
            ->where('year', $year);

        // unidade
        if (!empty($filters['unit_id'])) {
            $query->whereHas('classOffering', fn($q) =>
                $q->where('unit_id', $filters['unit_id'])
            );
        }

        // curso
        if (!empty($filters['course_id'])) {
            $query->whereHas('classOffering', fn($q) =>
                $q->where('course_id', $filters['course_id'])
            );
        }

        // status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $total = (clone $query)->sum('amount');

        $paid = (clone $query)
            ->where('status', 'paid')
            ->sum('amount');

        $pending = (clone $query)
            ->where('status', '!=', 'paid')
            ->sum('amount');

        $count = (clone $query)->count();

        return [
            'month' => $month,
            'year'  => $year,
            'total' => $total,
            'paid' => $paid,
            'pending' => $pending,
            'count' => $count,
        ];
    }
}