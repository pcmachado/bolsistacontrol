<?php

namespace App\DataTables;

use App\Models\ClassOffering;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;

class ClassOfferingsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('course', fn($row) => $row->course->name)
            ->addColumn('unit', fn($row) => $row->unit->name)
            ->addColumn('project', fn($row) => $row->project->name ?? '-')
            ->addColumn('period', fn($row) => $row->semester ? "{$row->semester}/{$row->year}" : $row->year)
            ->editColumn('status', fn($row) => ucfirst($row->status))
            ->addColumn('actions', fn($row) =>
                view('admin.class_offerings.partials.actions', compact('row'))
            )
            ->rawColumns(['actions'])
            ->setRowId('id');
    }

    public function query(ClassOffering $model)
    {
        return $model->newQuery()->with(['course', 'unit', 'project']);
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('class-offerings-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1, 'asc')
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('print'),
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('name')->title('Turma'),
            Column::computed('course')->title('Curso'),
            Column::computed('unit')->title('Unidade'),
            Column::computed('project')->title('Projeto'),
            Column::computed('period')->title('Período'),
            Column::make('status')->title('Status'),
            Column::computed('actions')
                ->title('Ações')
                ->exportable(false)
                ->printable(false)
                ->width(130)
                ->addClass('text-center'),
        ];
    }
}
