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
            ->orderBy(0, 'asc')
            ->parameters($this->defaultParameters());
    }

    protected function getColumns(): array
    {
        return [
            ['data' => 'id', 'title' => 'ID'],
            ['data' => 'name', 'title' => 'Nome'],
            ['data' => 'description', 'title' => 'Descrição'],
            [
                'data' => 'institution',
                'title' => 'Instituição',
                'orderable' => true,
                'searchable' => true
            ],
            [
                'data' => 'units',
                'title' => 'Unidades',
                'orderable' => false,
                'searchable' => false
            ],
            ['data' => 'start_date', 'title' => 'Data Início'],
            ['data' => 'end_date', 'title' => 'Data Término'],
            ['data' => 'created_at', 'title' => 'Criado em'],
            ['data' => 'updated_at', 'title' => 'Atualizado em'],
            [
                'data' => 'actions',
                'title' => 'Ações',
                'orderable' => false,
                'searchable' => false
            ],
        ];
    }

    protected function filename(): string
    {
        return 'Projects_'.date('YmdHis');
    }
}
