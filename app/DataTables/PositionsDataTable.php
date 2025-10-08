<?php
namespace App\DataTables;

use App\Models\Position;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class PositionsDataTable extends DataTable
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
            ->addColumn('actions', 'admin.positions.partials.actions') // Usando uma view para as ações
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
            ->dom('Bfrtip')
            ->orderBy(0, 'asc')
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
            ])
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