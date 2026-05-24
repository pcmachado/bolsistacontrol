<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class UsersDataTable extends BaseDataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addColumn('roles', function ($user) {
                $role = $user->getRoleNames()->first();

                if (! $role) {
                    return '<span class="text-muted">Sem papel</span>';
                }

                return '<span class="badge bg-primary-subtle text-primary-emphasis">'.$role.'</span>';
            })
            ->editColumn('created_at', fn ($user) => formatDate($user->created_at))
            ->editColumn('updated_at', fn ($user) => formatDate($user->updated_at))
            ->addColumn('unit', fn ($user) => $user->unit->name ?? 'N/A')
            ->addColumn('actions', fn ($user) => view('admin.users.partials.actions', compact('user')))
            ->rawColumns(['actions', 'roles']);
    }

    public function query(User $model)
    {
        $query = $model->newQuery()->with(['roles', 'unit']);

        $query = $this->applyInstitutionFilter($query);

        return $this->applyCustomFilters($query, [
            'filter_name',
            'filter_unit',
            'filter_role',
        ]);
    }

    protected function applyFilterNameFilter($query, $value)
    {
        return $query->where(function ($scoped) use ($value) {
            $scoped->where('name', 'like', "%{$value}%")
                ->orWhere('email', 'like', "%{$value}%");
        });
    }

    protected function applyFilterUnitFilter($query, $value)
    {
        return $query->where('unit_id', $value);
    }

    protected function applyFilterRoleFilter($query, $value)
    {
        return $query->whereHas('roles', function ($roleQuery) use ($value) {
            $roleQuery->where('name', $value);
        });
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax(request()->fullUrl())
            ->orderBy(0, 'asc')
            ->parameters(array_merge($this->defaultParameters(), [
                'pageLength' => (int) request('page_length', 25),
            ]))
            ->buttons([
                Button::make('excel')->className('btn btn-success')->text('Excel'),
                Button::make('csv')->className('btn btn-info')->text('CSV'),
                Button::make('pdf')->className('btn btn-warning')->text('PDF'),
                Button::make('print')->className('btn btn-secondary')->text('Imprimir'),
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('name')->title('Nome'),
            Column::make('email')->title('E-mail'),
            Column::make('roles')->title('Cargo / Papel')->orderable(false)->searchable(false),
            Column::make('unit')->title('Unidade')->orderable(false)->searchable(false),
            Column::make('created_at')->title('Criado Em'),
            Column::make('updated_at')->title('Atualizado Em'),
            Column::computed('actions')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center')
                ->title('Acoes'),
        ];
    }

    protected function filename(): string
    {
        return 'Users_'.date('YmdHis');
    }
}
