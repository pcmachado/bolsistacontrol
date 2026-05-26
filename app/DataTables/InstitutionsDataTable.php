<?php

namespace App\DataTables;

use App\Models\Institution;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use App\DataTables\BaseDataTable;

class InstitutionsDataTable extends BaseDataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('actions', fn ($institution) => view('admin.institutions.partials.actions', compact('institution'))->render()
            )
            ->rawColumns(['actions']);
    }

    public function query(Institution $model): Builder
    {
        return $model->newQuery()->latest();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('institutions-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'asc')
            ->parameters($this->defaultParameters());
    }

    protected function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('name')->title('Nome'),
            Column::make('cnpj')->title('CNPJ'),
            Column::make('city')->title('Cidade'),
            Column::make('state')->title('UF'),
            Column::computed('actions')
                ->exportable(false)
                ->printable(false)
                ->width(150)
                ->addClass('text-center')
                ->title('Ações'),
        ];
    }
}
