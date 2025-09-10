<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Html\Editor\Editor;

class UsersDataTable extends DataTable
{
    /**
     * Constrói a consulta para o Datatable.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return User::query()->select('id', 'name', 'email', 'role', 'created_at');
    }

    /**
     * Define as colunas para o Datatable.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('name')->title('Nome'),
            Column::make('email')->title('Email'),
            Column::make('role')->title('Papel'),
            Column::make('created_at')->title('Data de Criação'),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(120)
                  ->addClass('text-center')
                  ->title('Ações'),
        ];
    }

    /**
     * Prepara a saída para o Datatable.
     *
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse|\Yajra\DataTables\DataTableAbstract
     */
    public function ajax()
    {
        return datatables($this->query())
            ->addColumn('action', function($user){
                $editUrl = route('admin.users.edit', $user->id);
                $deleteUrl = route('admin.users.destroy', $user->id);
                return "<a href='{$editUrl}' class='btn btn-sm btn-info'>Editar</a> " .
                       "<form action='{$deleteUrl}' method='POST' style='display:inline;'> " .
                       csrf_field() . method_field('DELETE') .
                       "<button type='submit' class='btn btn-sm btn-danger'>Deletar</button></form>";
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
