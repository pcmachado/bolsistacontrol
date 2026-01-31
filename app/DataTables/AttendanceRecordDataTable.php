<?php

namespace App\DataTables;

use App\Models\AttendanceRecord;
use App\Services\AttendanceVisibilityService;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class AttendanceRecordDataTable extends DataTable
{
    public string $mode = 'default';
    protected array $filters = [];

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('date', fn ($r) => $r->date->format('d/m/Y'))
            ->addColumn('hours', fn ($r) => number_format($r->hours, 2))
            ->addColumn('submission', fn ($r) =>
                $r->attendance_submission_id
                    ? 'Enviado'
                    : 'Rascunho'
            )
            ->addColumn('actions', function ($record) {
                return view(
                    'attendance.partials.actions',
                    compact('record')
                );
            })
            ->rawColumns(['actions']);
    }

    public function query(AttendanceRecord $model)
    {
        $user = auth()->user();

        $query = $model->newQuery()
            ->with(['scholarshipHolder.user']);

        // 🔐 Visibilidade centralizada
        app(AttendanceVisibilityService::class)
            ->apply($query, $user);

        // 🔎 Filtros
        if (!empty($this->filters['month'])) {
            [$year, $month] = explode('-', $this->filters['month']);
            $query->whereYear('date', $year)
                  ->whereMonth('date', $month);
        }

        if (!empty($this->filters['status'])) {
            if ($this->filters['status'] === 'draft') {
                $query->whereNull('attendance_submission_id');
            }

            if ($this->filters['status'] === 'submitted') {
                $query->whereNotNull('attendance_submission_id');
            }
        }

        return $query->latest('date');
    }
}
