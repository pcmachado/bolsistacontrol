<?php
namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('role', function ($user) {
                // Assumindo que você está a usar o pacote Spatie/Permission
                return $user->getRoleNames()->first() ?? 'N/A';
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
            Column::make('email')->title('E-mail'),
            Column::make('role')->title('Papel')->orderable(false)->searchable(false),
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