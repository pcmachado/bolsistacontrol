<?php
namespace App\DataTables;

use App\Models\Project;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Button;

class ProjectsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
        ->setRowId('id')
        ->addColumn('name', function ($project) {
            return $project->name ?? 'N/A';
        })
        ->addColumn('description', function ($project) {
            return $project->description ?? 'N/A';
        })
        ->addColumn('institution', function ($project) {
            return $project->institution->name ?? 'N/A';
        })
        ->addColumn('start_date', function ($project) {
            return formatDate($project->start_date);
        })
        ->addColumn('end_date', function ($project) {
            return formatDate($project->end_date);
        })
        ->addColumn('created_at', function ($project) {
            return formatDate($project->created_at);
        })
        ->addColumn('updated_at', function ($project) {
            return formatDate($project->updated_at);
        })
        ->addColumn('actions', 'admin.projects.partials.actions')
        ->rawColumns(['actions']);
    }

    public function query(Project $model)
    {
        return $model->newQuery()->with(['institution']);
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('projects-table')
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
        Column::make('description')->title('Descrição'),
        Column::make('institution')->title('Instituição'),
        Column::make('start_date')->title('Data de Início'),
        Column::make('end_date')->title('Data de Término'),
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
        return 'Projects_' . date('YmdHis');
    }
}