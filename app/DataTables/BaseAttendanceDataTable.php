<?php

namespace App\DataTables;

use App\Models\AttendanceSubmission;
use Yajra\DataTables\EloquentDataTable;

abstract class BaseAttendanceDataTable extends BaseDataTable
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
            ->addColumn('project', fn ($record) => $record->project?->name ?? '-')
            ->addColumn('date', fn ($record) => $record->date->format('d/m/Y'))
            ->addColumn('hours', fn ($record) => number_format($record->hours, 2))
            ->addColumn('status', fn ($record) => $record->status_label)
            ->addColumn('actions', fn ($record) =>
                view('attendance.partials.actions', compact('record'))
            )
            ->rawColumns(['actions']);
    }

    protected function getColumns(): array
    {
        return [
            ['data' => 'project', 'title' => 'Projeto'],
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
        if (! empty($this->filters['month'])) {
            [$year, $month] = explode('-', $this->filters['month']);

            $query->whereYear('date', $year)
                ->whereMonth('date', $month);
        }

        if (! empty($this->filters['project_id'])) {
            $query->where('project_id', $this->filters['project_id']);
        }

        if (! empty($this->filters['status'])) {
            $status = $this->filters['status'];

            if ($status === AttendanceSubmission::STATUS_DRAFT) {
                $query->where(function ($builder) {
                    $builder->whereDoesntHave('submission')
                        ->orWhereHas('submission', fn ($submission) =>
                            $submission->where('status', AttendanceSubmission::STATUS_DRAFT)
                        );
                });
            } else {
                $query->whereHas('submission', fn ($builder) =>
                    $builder->where('status', $status)
                );
            }
        }

        return $query;
    }
}
