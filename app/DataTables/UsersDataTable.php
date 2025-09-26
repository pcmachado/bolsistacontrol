<?php
namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Button;

class UsersDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('role', function ($user) {
                return $user->getRoleNames()->implode(', ');
            })
            ->addColumn('units', function ($user) {
                // Pega os nomes das unidades e junta-os com uma vírgula
                return $user->units->pluck('nome')->implode(', ');
            })
            ->addColumn('actions', 'admin.users.partials.actions') // Usando uma view para as ações
            ->rawColumns(['actions']);
    }

    public function query(User $model)
    {
        return $model->newQuery();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0, 'asc')
            ->parameters([
            'responsive' => true,
            'autoWidth' => false,
            'language' => [
                'url' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            ],
        ])
        ->buttons([
            Button::make('excel')->className('btn btn-success')->text('📊 Excel'),
            Button::make('csv')->className('btn btn-info')->text('📝 CSV'),
            Button::make('print')->className('btn btn-secondary')->text('🖨️ Imprimir'),
            Button::make('reload')->className('btn btn-dark')->text('🔄 Recarregar'),
        ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('name')->title('Nome'),
            Column::make('email')->title('E-mail'),
            Column::make('roles')->title('Papeis')->orderable(false)->searchable(false),
            Column::make('units')->title('Unidades')->orderable(false)->searchable(false), // Adiciona a coluna de unidades
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
        return 'Users_' . date('YmdHis');
    }
}