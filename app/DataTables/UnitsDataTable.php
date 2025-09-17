<?php
namespace App\DataTables;

use App\Models\Unit;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UnitsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
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
            ->buttons([
                Button::make('excel')->title('Exportar para Excel'),
                Button::make('csv')->title('Exportar para CSV'),
                Button::make('print')->title('Imprimir'),
                Button::make('reload')->title('Recarregar'),
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