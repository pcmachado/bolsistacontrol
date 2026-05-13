<?php

namespace App\DataTables;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\DataTables\BaseDataTable;
use App\Services\VisibilityService;

class CoursesDataTable extends BaseDataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('units', function ($course) {
                return $course->classOfferings
                    ->pluck('unit.name')
                    ->unique()
                    ->implode('<br>');
            })
            ->addColumn('projects', function ($course) {
                $projectNames = $course->projects
                    ->pluck('name')
                    ->unique()
                    ->filter();

                if ($projectNames->isEmpty()) {
                    $projectNames = $course->classOfferings
                        ->pluck('project.name')
                        ->unique()
                        ->filter();
                }

                return $projectNames->implode('<br>');
            })
            ->addColumn('offerings_count', fn ($course) => $course->classOfferings->count())
            ->addColumn('actions', fn ($course) => view('admin.courses.partials.actions', compact('course')))
            ->rawColumns(['units', 'projects', 'actions'])
            ->setRowId('id');
    }

    public function query(Course $model)
    {
        $user = Auth::user();

        $query = $model->newQuery()->with('classOfferings.unit', 'classOfferings.project', 'projects');

        $query = app(VisibilityService::class)
            ->apply($query, $user, 'admin');

        if (! empty($this->filters['unit_id'])) {
            $unitId = (int) $this->filters['unit_id'];

            $query->whereHas('classOfferings', function ($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            });
        }

        if (! empty($this->filters['project_id'])) {
            $projectId = (int) $this->filters['project_id'];

            $query->where(function ($query) use ($projectId) {
                $query->whereHas('classOfferings', function ($q) use ($projectId) {
                    $q->where('project_id', $projectId);
                })->orWhereHas('projects', function ($q) use ($projectId) {
                    $q->where('projects.id', $projectId);
                });
            });
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('courses-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
            ->parameters($this->defaultParameters())
            ->buttons([
                Button::make('excel')->className('btn btn-success rounded-0'),
                Button::make('csv')->className('btn btn-info rounded-0'),
                Button::make('pdf')->className('btn btn-warning rounded-0'),
                Button::make('print')->className('btn btn-secondary rounded-0'),
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('name')->title('Curso'),
            Column::make('capacity')
                ->title('Capacidade')
                ->addClass('text-center'),
            Column::computed('units')
                ->title('Unidades')
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-start'),
            Column::computed('projects')
                ->title('Projetos')
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-start'),
            Column::computed('offerings_count')
                ->title('Turmas')
                ->addClass('text-center'),
            Column::computed('actions')
                ->title('Acoes')
                ->exportable(false)
                ->printable(false)
                ->width(160)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Cursos_' . date('YmdHis');
    }
}
