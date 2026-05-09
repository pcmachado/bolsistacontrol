<?php

namespace App\Support\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;

trait PaymentFilters
{
    public function applyPaymentFilters($query, Request $request)
    {
        /**
         * 📅 FILTRO DE MÊS / ANO
         */
        if ($request->filled('month')) {

            try {
                if (str_contains($request->month, '-')) {
                    // formato YYYY-MM
                    $date = Carbon::createFromFormat('Y-m', $request->month);
                } else {
                    // formato separado (month=5&year=2026)
                    $date = Carbon::create(
                        $request->year ?? now()->year,
                        $request->month
                    );
                }

                $query->where('year', $date->year)
                    ->where('month', $date->month);

            } catch (\Exception $e) {
                // evita quebrar aplicação
            }

        } elseif ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        /**
         * 🏢 UNIDADE
         */
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        /**
         * 📁 PROJETO
         */
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        /**
         * 🎯 FUNÇÃO (POSITION via pivot)
         */
        if ($request->filled('position_id')) {
            $query->whereHas('scholarshipHolder.projects', function ($q) use ($request) {
                $q->wherePivot('position_id', $request->position_id);
            });
        }

        /**
         * 📌 STATUS
         */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query;
    }
}
