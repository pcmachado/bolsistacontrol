<?php

namespace App\DataTables;

use App\Models\ScholarshipHolder;
use App\Services\VisibilityService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class ScholarshipHoldersDataTable extends BaseDataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addColumn('linked_user', function ($scholarshipHolder) {
                return $scholarshipHolder->user->name ?? 'N/A';
            })
            ->editColumn('cpf', function ($scholarshipHolder) {
                return $scholarshipHolder->cpf ?: 'N/A';
            })
            ->addColumn('unit', function ($scholarshipHolder) {
                return $scholarshipHolder->unit->name ?? 'N/A';
            })
            ->addColumn('positions', function ($scholarshipHolder) {
                $positions = $scholarshipHolder->projects
                    ->sortBy('name')
                    ->map(function ($project) {
                        if (($project->pivot->status ?? null) !== 'active') {
                            return null;
                        }

                        return optional(
                            $project->positions->firstWhere('id', $project->pivot->position_id)
                        )->name;
                    })
                    ->filter()
                    ->unique()
                    ->values();

                if ($positions->isEmpty()) {
                    $positions = $scholarshipHolder->projects
                        ->sortBy('name')
                        ->map(fn ($project) => optional(
                            $project->positions->firstWhere('id', $project->pivot->position_id)
                        )->name)
                        ->filter()
                        ->unique()
                        ->values();
                }

                if ($positions->isEmpty()) {
                    return '<span class="text-muted">Sem cargo vinculado</span>';
                }

                return $positions
                    ->map(fn ($name) => '<span class="badge bg-light text-dark border me-1 mb-1">'.$name.'</span>')
                    ->implode(' ');
            })
            ->editColumn('status', function ($scholarshipHolder) {
                $isActive = $scholarshipHolder->status === 'active';

                return sprintf(
                    '<span class="badge %s">%s</span>',
                    $isActive ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis',
                    $isActive ? 'Ativo' : 'Inativo'
                );
            })
            ->addColumn('actions', 'admin.scholarship_holders.partials.actions')
            ->rawColumns(['actions', 'positions', 'status']);
    }

    public function query(ScholarshipHolder $model)
    {
        $user = Auth::user();

        $query = $model->newQuery()
            ->with([
                'user',
                'unit',
                'projects' => fn ($projectQuery) => $projectQuery
                    ->whereNull('project_scholarship_holder.deleted_at')
                    ->with('positions'),
            ]);

        $query = app(VisibilityService::class)->apply($query, $user, 'admin');

        return $this->applyCustomFilters($query, [
            'filter_name',
            'filter_unit',
            'filter_position',
        ]);
    }

    protected function applyFilterNameFilter($query, $value)
    {
        return $query->where(function ($scoped) use ($value) {
            $scoped->where('name', 'like', "%{$value}%")
                ->orWhere('email', 'like', "%{$value}%")
                ->orWhere('cpf', 'like', "%{$value}%")
                ->orWhereHas('user', function ($userQuery) use ($value) {
                    $userQuery->where('name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%");
                });
        });
    }

    protected function applyFilterUnitFilter($query, $value)
    {
        return $query->where('unit_id', $value);
    }

    protected function applyFilterPositionFilter($query, $value)
    {
        return $query->whereHas('projects', function ($projectQuery) use ($value) {
            $projectQuery->whereNull('project_scholarship_holder.deleted_at')
                ->where('project_scholarship_holder.position_id', $value);
        });
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('scholarship_holders-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('admin.scholarship_holders.index'))
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
            Column::make('cpf')->title('CPF'),
            Column::make('email')->title('E-mail'),
            Column::make('linked_user')->title('Usuário vinculado')->orderable(false)->searchable(false),
            Column::make('unit')->title('Unidade')->orderable(false)->searchable(false),
            Column::computed('positions')->title('Cargo(s)')->orderable(false)->searchable(false),
            Column::make('status')->title('Status')->orderable(false)->searchable(false),
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
        return 'ScholarshipHolders_'.date('YmdHis');
    }
}
