<?php
namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Button;
use Illuminate\Support\Facades\Auth;

class UsersDataTable extends DataTable
{
    protected array $filters = [];

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
        ->setRowId('id')

        ->addColumn('roles', function ($user) {
            // Pega o primeiro papel do utilizador e exibe o nome
            return $user->getRoleNames()->first() ?? 'N/A';
        })

        ->editColumn('created_at', fn($user) => formatDate($user->created_at))
        ->editColumn('updated_at', fn($user) => formatDate($user->updated_at))

        ->addColumn('unit', fn ($user) => $user->unit->name ?? 'N/A')

        ->addColumn('actions', function ($user) {
            return view('admin.users.partials.actions', compact('user'));
        })
        ->rawColumns(['actions']);
    }

    public function query(User $model)
    {
        $logged = Auth::user();

        $query = $model->newQuery()->with(['roles', 'unit']);

        // superadmin / admin → vê tudo
        if ($logged->hasAnyRole(['superadmin', 'admin'])) {
            // sem restrição
        }
        // coordenação geral → só instituição
        elseif ($logged->hasAnyRole(['coordenador_geral', 'coordenador_adjunto_geral'])) {

            $institutionId = $logged->institution_id;

            $query->where(function ($q) use ($institutionId) {
                $q->whereHas('unit', fn ($u) =>
                    $u->where('institution_id', $institutionId)
                )
                // inclui coordenadores gerais (sem unidade)
                ->orWhere(function ($qq) use ($institutionId) {
                    $qq->whereNull('unit_id')
                       ->where('institution_id', $institutionId);
                });
            });

            // não vê admins
            $query->whereDoesntHave('roles', fn ($q) =>
                $q->whereIn('name', ['admin', 'superadmin'])
            );
        }
        // coordenador adjunto → só unidade
        elseif ($logged->hasRole('coordenador_adjunto')) {
            $query->where('unit_id', $logged->unit_id)
                ->whereDoesntHave('roles', fn ($q) =>
                    $q->whereIn('name', [
                        'admin',
                        'superadmin',
                        'coordenador_geral',
                        'coordenador_adjunto_geral',
                    ])
                );
        }
        // fallback defensivo (outros papéis)
        else {
            $query->where('id', $logged->id);
        }

        // Nome
        if (! empty($this->filters['filter_name'])) {
            $query->where('name', 'like', '%' . $this->filters['filter_name'] . '%');
        }

        // Unidade
        if (! empty($this->filters['filter_unit'])) {
            $query->where('unit_id', $this->filters['filter_unit']);
        }

        // Cargo (role)
        if (! empty($this->filters['filter_role'])) {
            $query->whereHas('roles', fn($q) =>
                $q->where('name', $this->filters['filter_role'])
            );
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax(request()->fullUrl())
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

            Column::make('roles')
                ->title('Papel')
                ->orderable(false)
                ->searchable(false),

            Column::make('unit')
                ->title('Unidade')
                ->orderable(false)
                ->searchable(false),

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
