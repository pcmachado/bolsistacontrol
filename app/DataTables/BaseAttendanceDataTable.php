<?php

namespace App\DataTables;

use App\Models\AttendanceRecord;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

abstract class BaseAttendanceDataTable extends DataTable
{
    protected array $filters = [];

    public function setFilters(array $filters): static
    {
        $this->filters = $filters;
        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | COMMON COLUMNS
    |--------------------------------------------------------------------------
    */

    protected function buildDataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('date', fn ($r) => $r->date->format('d/m/Y'))
            ->addColumn('hours', fn ($r) => number_format($r->hours, 2))
            ->addColumn('status', fn ($r) => $r->status_label)
            ->addColumn('actions', fn ($record) =>
                view('attendance.partials.actions', compact('record'))
            )
            ->rawColumns(['actions']);
    }

    protected function getColumns(): array
    {
        return [
            ['data' => 'date', 'title' => 'Data'],
            ['data' => 'hours', 'title' => 'Horas'],
            ['data' => 'status', 'title' => 'Situação'],
            [
                'data' => 'actions',
                'title' => 'Ações',
                'orderable' => false,
                'searchable' => false,
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | COMMON FILTERS
    |--------------------------------------------------------------------------
    */

    protected function applyFilters($query)
    {
        // 📅 filtro por mês
        if (!empty($this->filters['month'])) {
            [$year, $month] = explode('-', $this->filters['month']);

            $query->whereYear('date', $year)
                  ->whereMonth('date', $month);
        }

        // 📌 filtro por status (submission)
        if (!empty($this->filters['status'])) {
            $status = $this->filters['status'];

            if ($status === 'draft') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('submission')
                      ->orWhereHas('submission', fn ($sub) =>
                          $sub->where('status', 'draft')
                      );
                });
            } else {
                $query->whereHas('submission', fn ($q) =>
                    $q->where('status', $status)
                );
            }
        }

        return $query;
    }
}