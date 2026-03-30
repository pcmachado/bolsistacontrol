<?php

namespace App\Support\Traits;

use Illuminate\Http\Request;

trait PaymentFilters
{
    public function applyPaymentFilters($query, Request $request)
    {
        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);

            $query->where('year', $year)
                  ->where('month', $month);
        } elseif ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('position_id')) {
            $query->whereHas('scholarshipHolder.projects', function ($q) use ($request) {
                $q->where('position_id', $request->position_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query;
    }
}