<?php

namespace App\DataTables;

use App\Models\ScholarshipHolder;
use App\Models\ClassOffering;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class ClassOfferingScholarshipHoldersDataTable extends BaseDataTable
{
    protected $offering;

    public function setOffering(ClassOffering $offering)
    {
        $this->offering = $offering;
        return $this;
    }

    public function dataTable($query)
    {
        return (new EloquentDataTable($query))

            ->addColumn('name', fn($row) => $row->user->name)
            ->addColumn('email', fn($row) => $row->user->email)
            ->addColumn('unit', fn($row) => $row->unit->name ?? '-')

            ->editColumn('role', fn($row) => $row->pivot->role ?? '-')

            ->addColumn('actions', function ($row) {
                return view('admin.class-offerings.scholarship_holders.partials.actions', [
                    'student' => $row,
                    'offering' => $this->offering,
                ]);
            })

            ->rawColumns(['actions']);
    }

    public function query(ScholarshipHolder $model)
    {
        $query = $this->offering
            ->scholarshipHolders()
            ->with(['user', 'unit'])
            ->getQuery();

        // Filtros opcionais
        if ($role = request('filter_role')) {
            $query->wherePivot('role', $role);
        }

        if ($unit = request('filter_unit')) {
            $query->where('unit_id', $unit);
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('scholarship-holders-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
            ->parameters($this->defaultParameters())
            ->buttons([
                'excel', 'csv', 'pdf', 'print'
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('name')->title('Nome'),
            Column::computed('email')->title('E-mail'),
            Column::computed('unit')->title('Unidade'),
            Column::computed('role')->title('Papel'),

            Column::computed('actions')
                ->title('Ações')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'BolsistasTurma_' . date('YmdHis');
    }
}
