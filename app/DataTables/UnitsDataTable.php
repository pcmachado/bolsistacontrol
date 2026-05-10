<?php
namespace App\DataTables;

use App\Models\Unit;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\EloquentDataTable;

class UnitsDataTable extends BaseDataTable
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
            ->addColumn('actions', 'admin.units.partials.actions') // Usando uma view para as ações
            ->rawColumns(['actions']);
    }

    public function query(Unit $model)
    {
        return $model->newQuery();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('units-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0, 'asc')
            ->parameters($this->defaultParameters())
            ->buttons([
                Button::make('excel')->className('btn btn-success rounded-0')->text('📊 Excel'),
                Button::make('csv')->className('btn btn-info rounded-0')->text('📝 CSV'),
                Button::make('pdf')->className('btn btn-warning rounded-0')->text('📄 PDF'),
                Button::make('print')->className('btn btn-secondary rounded-0')->text('🖨️ Imprimir'),
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('name')->title('Nome'),
            Column::make('city')->title('Cidade'),
            Column::make('address')->title('Endereço'),
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
        return 'Units_' . date('YmdHis');
    }
}