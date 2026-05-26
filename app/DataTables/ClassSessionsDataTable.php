<?php

namespace App\DataTables;

use App\Models\ClassSession;
use App\Models\ClassOffering;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;

class ClassSessionsDataTable extends BaseDataTable
{
    protected ClassOffering $offering;
    protected array $filters = [];

    public function setOffering(ClassOffering $offering): self
    {
        $this->offering = $offering;
        return $this;
    }

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function dataTable($query)
    {
        return (new EloquentDataTable($query))

            ->editColumn('date', fn($row) => $row->date->format('d/m/Y'))

            ->addColumn('discipline', fn($row) => $row->discipline->name)
            ->addColumn('teacher', fn($row) => $row->teacher->name)

            ->editColumn('status', function ($row) {
                return match ($row->status) {
                    'planned'   => '<span class="badge bg-secondary">Planejada</span>',
                    'finished'  => '<span class="badge bg-success">Realizada</span>',
                    'cancelled' => '<span class="badge bg-danger">Cancelada</span>',
                    default     => $row->status
                };
            })

            ->addColumn('schedule', fn($row) => "{$row->start_time} - {$row->end_time}")

            ->addColumn('actions', fn($row) =>
                view('admin.class-offerings.sessions.partials.actions', [
                    'session' => $row,
                    'offering' => $this->offering,
                ])
            )

            ->rawColumns(['status', 'actions']);
    }

    public function query(ClassSession $model)
    {
        $query = $model->newQuery()
            ->where('class_offering_id', $this->offering->id)
            ->with(['discipline', 'teacher']);

        // FILTROS
        if ($disc = ($this->filters['filter_discipline'] ?? null)) {
            $query->where('discipline_id', $disc);
        }

        if ($teacher = ($this->filters['filter_teacher'] ?? null)) {
            $query->where('teacher_id', $teacher);
        }

        if ($status = ($this->filters['filter_status'] ?? null)) {
            $query->where('status', $status);
        }

        if ($from = ($this->filters['filter_from'] ?? null)) {
            $query->whereDate('date', '>=', $from);
        }

        if ($to = ($this->filters['filter_to'] ?? null)) {
            $query->whereDate('date', '<=', $to);
        }

        if ($min = ($this->filters['filter_min_hours'] ?? null)) {
            $query->where('duration_hours', '>=', $min);
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('class-sessions-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('admin.class-offerings.sessions.index', $this->offering->id))
            ->orderBy(0)
            ->parameters($this->defaultParameters())
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('date')->title('Data'),
            Column::computed('discipline')->title('Disciplina'),
            Column::computed('teacher')->title('Professor'),
            Column::computed('schedule')->title('Horário'),
            Column::make('duration_hours')->title('Horas')->addClass('text-center'),

            Column::computed('status')->title('Status'),

            Column::computed('actions')
                ->title('Ações')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center')
                ->width(120),
        ];
    }

    protected function filename(): string
    {
        return 'AulasTurma_' . date('YmdHis');
    }
}
