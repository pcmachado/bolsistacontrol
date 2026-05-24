<?php
namespace App\DataTables;

use App\Models\Position;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use App\DataTables\BaseDataTable;

class PositionsDataTable extends BaseDataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addColumn('created_at', function ($unit) {
                return formatDate($unit->created_at);
            })
            ->addColumn('updated_at', function ($unit) {
                return formatDate($unit->updated_at);
            })
            ->addColumn('actions', 'admin.positions.partials.actions')
            ->rawColumns(['actions']);
    }

    public function query(Position $model)
    {
        return $model->newQuery();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('positions-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'asc')
            ->parameters($this->defaultParameters())
            ->buttons([
                Button::make('excel')->className('btn btn-success')->text('📊 Excel'),
                Button::make('csv')->className('btn btn-info')->text('📝 CSV'),
                Button::make('pdf')->className('btn btn-warning')->text('📄 PDF'),
                Button::make('print')->className('btn btn-secondary')->text('🖨️ Imprimir'),
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('name')->title('Nome'),
            Column::make('description')->title('Descrição'),
            Column::make('created_at')->title('Criado Em'),
            Column::make('updated_at')->title('Atualizado Em'),
            Column::computed('actions')
                ->exportable(false)
                ->printable(false)
                ->width(150)
                ->addClass('text-center')
                ->title('Ações'),
        ];
    }

    protected function filename(): string
    {
        return 'Positions_' . date('YmdHis');
    }
}