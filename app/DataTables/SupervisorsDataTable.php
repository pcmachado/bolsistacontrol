<?php

namespace App\DataTables;

use App\Models\SupervisorAssignment;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;

class SupervisorsDataTable extends DataTable
{
    protected array $filters = [];

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function dataTable($query)
    {
        return (new EloquentDataTable($query))

            ->addColumn('supervisor', fn($row) => $row->supervisor->name)
            ->addColumn('course', fn($row) => $row->course->name)
            ->addColumn('unit', fn($row) => $row->unit->name)
            ->editColumn('active', function ($row) {
                return $row->active
                    ? '<span class="badge bg-success">Ativo</span>'
                    : '<span class="badge bg-secondary">Inativo</span>';
            })

            ->addColumn('actions', function ($row) {
                return view('admin.supervisors.partials.actions', compact('row'));
            })

            ->rawColumns(['active', 'actions'])
            ->setRowId('id');
    }

    public function query(SupervisorAssignment $model)
    {
        $query = $model->newQuery()->with(['supervisor', 'course', 'unit']);

        // FILTROS AVANÇADOS

        if ($unit = ($this->filters['filter_unit'] ?? null)) {
            $query->where('unit_id', $unit);
        }

        if ($course = ($this->filters['filter_course'] ?? null)) {
            $query->where('course_id', $course);
        }

        if ($status = ($this->filters['filter_status'] ?? null)) {
            $query->where('active', $status === 'active');
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('supervisors-table')
            ->columns($this->getColumns())
            ->minifiedAjax(request()->fullUrl())
            ->dom('Bfrtip')
            ->orderBy(0)
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::computed('supervisor')->title('Supervisor'),
            Column::computed('course')->title('Curso'),
            Column::computed('unit')->title('Unidade'),
            Column::computed('active')->title('Status'),

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
        return 'Supervisores_' . date('YmdHis');
    }
}
