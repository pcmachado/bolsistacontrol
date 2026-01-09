<?php

namespace App\DataTables;

use App\Models\Course;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CoursesDataTable extends DataTable
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
                return $course->classOfferings
                    ->pluck('project.name')
                    ->unique()
                    ->filter() // remove null
                    ->implode('<br>');
            })

            ->addColumn('offerings_count', function ($course) {
                return $course->classOfferings->count();
            })

            ->addColumn('actions', function ($course) {
                return view('admin.courses.partials.actions', compact('course'));
            })

            ->rawColumns(['units', 'projects', 'actions'])
            ->setRowId('id');
    }

    public function query(Course $model)
    {
        $query = $model->newQuery()
            ->visibleForUser(Auth::user())
            ->with('classOfferings.unit', 'classOfferings.project');

        if ($unit = request('filter_unit')) {
            $query->whereHas('classOfferings', fn ($q) =>
                $q->where('unit_id', $unit)
            );
        }

        if ($project = request('filter_project')) {
            $query->whereHas('classOfferings', fn ($q) =>
                $q->where('project_id', $project)
            );
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('courses-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0)
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
                ->title('Ações')
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
