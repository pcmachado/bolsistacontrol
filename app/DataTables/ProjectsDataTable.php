<?php

namespace App\DataTables;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\DataTables\BaseDataTable;

class ProjectsDataTable extends BaseDataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addColumn('name', fn ($project) => $project->name ?? 'N/A')
            ->addColumn('description', fn ($project) => $project->description ?? 'N/A')
            ->addColumn('institution', fn ($project) => $project->institution->name ?? 'N/A')
            ->addColumn('units', function ($project) {
                return $project->units
                    ->pluck('name')
                    ->unique()
                    ->implode('<br>') ?: '-';
            })
            ->addColumn('start_date', fn ($project) => formatDate($project->start_date))
            ->addColumn('end_date', fn ($project) => formatDate($project->end_date))
            ->addColumn('created_at', fn ($project) => formatDate($project->created_at))
            ->addColumn('updated_at', fn ($project) => formatDate($project->updated_at))
            ->addColumn('actions', 'admin.projects.partials.actions')
            ->rawColumns(['units', 'actions']);
    }

    public function query(Project $model)
    {
        $user = Auth::user();

        $query = $model->newQuery()
            ->with(['institution', 'units'])
            ->select('projects.*');

        if ($user->isInstitutionScoped()) {
            return $query->whereIn('projects.institution_id', $user->activeInstitutionIds());
        }

        if ($user->isUnitScoped()) {
            return $query->whereHas('units', fn ($q) => $q->whereIn('units.id', $user->visibleUnitIds())
            );
        }

        $projectIds = $user->visibleProjectIds();

        if ($projectIds->isNotEmpty()) {
            return $query->whereIn('projects.id', $projectIds);
        }

        return $query->whereRaw('1 = 0');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('projects-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0, 'asc')
            ->parameters($this->defaultParameters())
            ->buttons([
                Button::make('excel')->className('btn btn-success rounded-0')->text('Excel'),
                Button::make('csv')->className('btn btn-info rounded-0')->text('CSV'),
                Button::make('pdf')->className('btn btn-warning rounded-0')->text('PDF'),
                Button::make('print')->className('btn btn-secondary rounded-0')->text('Imprimir'),
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('name')->title('Nome'),
            Column::make('description')->title('Descricao'),
            Column::computed('institution')
                ->title('Instituicao')
                ->orderable(false)
                ->searchable(false),
            Column::computed('units')
                ->title('Unidades')
                ->orderable(false)
                ->searchable(false),
            Column::make('start_date')->title('Data de Inicio'),
            Column::make('end_date')->title('Data de Termino'),
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
        return 'Projects_'.date('YmdHis');
    }
}
