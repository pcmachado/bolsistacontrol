<?php

namespace App\DataTables;

use App\Models\ClassOffering;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;

class ClassOfferingsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))

            ->addColumn('course', fn($row) => $row->course->name)
            ->addColumn('unit', fn($row) => $row->unit->name)
            ->addColumn('project', fn($row) => $row->project->name ?? '-')

            // DISCIPLINAS
            ->addColumn('disciplines_count', fn($row) => $row->disciplines->count())

            // ALUNOS / BOLSISTAS
            ->addColumn('students_count', fn($row) => $row->scholarshipHolders->count())

            // STATUS (visual)
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                    'planned'   => '<span class="badge bg-secondary">Planejado</span>',
                    'ongoing'   => '<span class="badge bg-primary">Em andamento</span>',
                    'finished'  => '<span class="badge bg-success">Concluído</span>',
                    'cancelled' => '<span class="badge bg-danger">Cancelado</span>',
                    default     => $row->status,
                };
            })

            // AÇÕES
            ->addColumn('actions', fn($row) =>
                view('admin.class-offerings.partials.actions', compact('row'))
            )

            ->rawColumns(['status', 'actions'])
            ->setRowId('id');
    }

    public function query(ClassOffering $model)
    {
        $query = $model->newQuery()
            ->visibleForUser(auth()->user())
            ->with(['course', 'unit', 'project']);

        // FILTROS AVANÇADOS
        if ($course = request('filter_course')) {
            $query->where('course_id', $course);
        }

        if ($unit = request('filter_unit')) {
            $query->where('unit_id', $unit);
        }

        if ($project = request('filter_project')) {
            $query->where('project_id', $project);
        }

        if ($status = request('filter_status')) {
            $query->where('status', $status);
        }

        if ($year = request('filter_year')) {
            $query->where('year', $year);
        }

        if ($semester = request('filter_semester')) {
            $query->where('semester', 'like', "%$semester%");
        }

        if ($minStudents = request('filter_min_students')) {
            $query->whereHas('scholarshipHolders', function ($q) {
                $q->selectRaw('count(*)')
                  ->groupBy('class_offering_id');
            }, '>=', $minStudents);
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('class-offerings-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0, 'asc')
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('name')->title('Turma'),

            Column::computed('course')->title('Curso'),
            Column::computed('unit')->title('Unidade'),
            Column::computed('project')->title('Projeto'),

            Column::computed('disciplines_count')
                ->title('Disciplinas')
                ->addClass('text-center'),

            Column::computed('students_count')
                ->title('Bolsistas')
                ->addClass('text-center'),

            Column::computed('status')->title('Status'),

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
        return 'Turmas_' . date('YmdHis');
    }
}
