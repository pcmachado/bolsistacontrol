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
            ->addColumn('submission', function ($r) {
                if (! $r->submission) {
                    return 'Rascunho';
                }

                return match ($r->submission->status) {
                    'draft'     => 'Em edição',
                    'submitted' => 'Enviado',
                    'approved'  => 'Homologado',
                    'rejected'  => 'Rejeitado',
                    default     => '-',
                };
            })
            ->addColumn('status', function ($r) {
                if ($r->attendance_submission_id) {
                    return '<span class="badge bg-info">Vinculado ao mês</span>';
                }

                return '<span class="badge bg-secondary">Rascunho</span>';
            })
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
            ->with(['scholarshipHolder.user', 'submission']);

        // 🔐 Visibilidade centralizada
        app(AttendanceVisibilityService::class)
            ->apply($query, $user);

        // 🔎 Filtros
        if (!empty($this->filters['month'])) {

            if (! preg_match('/^\d{4}-\d{2}$/', $this->filters['month'])) {
                throw new \InvalidArgumentException('Formato de mês inválido.');
            }

            [$year, $month] = explode('-', $this->filters['month']);
            $query->whereYear('date', $year)
                  ->whereMonth('date', $month);
        }

        if (!empty($this->filters['status'])) {

            if ($this->filters['status'] === 'draft') {
                $query->whereDoesntHave('submission')
                    ->orWhereHas('submission', fn ($q) =>
                        $q->where('status', 'draft')
                    );
            }

            if ($this->filters['status'] === 'submitted') {
                $query->whereHas('submission', fn ($q) =>
                    $q->where('status', 'submitted')
                );
            }

            if ($this->filters['status'] === 'approved') {
                $query->whereHas('submission', fn ($q) =>
                    $q->where('status', 'approved')
                );
            }
        }

        return $query->latest('date');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('attendance-records-table')
            ->minifiedAjax()
            ->responsive(true)
            ->columns([
                ['data' => 'date',       'title' => 'Data'],
                ['data' => 'hours',      'title' => 'Horas'],
                ['data' => 'submission', 'title' => 'Situação'],
                [
                    'data'       => 'actions',
                    'title'      => 'Ações',
                    'orderable'  => false,
                    'searchable' => false,
                ],
            ]);
    }

}
