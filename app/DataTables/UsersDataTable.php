<?php
namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Button;

class UsersDataTable extends DataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
        ->setRowId('id')
        ->addColumn('roles', function ($user) {
            // Pega o primeiro papel do utilizador e exibe o nome
            return $user->getRoleNames()->first() ?? 'N/A';
        })
        ->editColumn('created_at', function ($user) {
            return formatDate($user->created_at);
        })
        ->editColumn('updated_at', function ($user) {
            return formatDate($user->updated_at);
        })
        ->addColumn('unit', function ($user) {
            return $user->unit->name ?? 'N/A';
        })
        ->addColumn('actions', 'admin.users.partials.actions')
        ->rawColumns(['actions']);
    }

    public function query(User $model)
    {
        return $model->newQuery()->with(['roles', 'unit']);
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
        Column::make('email')->title('E-mail'),
        Column::make('roles')->title('Papel')->orderable(false)->searchable(false), // Adiciona a coluna
        Column::make('unit')->title('Unidade')->orderable(false)->searchable(false), // Adiciona a coluna de unidades
        Column::make('created_at')->title('Criado Em'),
        Column::make('updated_at')->title('Atualizado Em'),
        Column::computed('actions')
              ->exportable(false)
              ->printable(false)
              ->width(120)
              ->addClass('text-center')
              ->title('Ações'),
    ];
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}