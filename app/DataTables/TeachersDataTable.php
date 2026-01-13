<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;

class TeachersDataTable extends DataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('unit', fn($t) => $t->unit->name ?? '-')

            ->addColumn('disciplines_count', function ($t) {
                return $t->teachingAssignments->count();
            })

            ->addColumn('actions', function ($t) {
                return view('admin.teachers.partials.actions', compact('t'));
            })

            ->rawColumns(['actions'])
            ->setRowId('id');
    }

    public function query(User $model)
    {
        $query = $model->role('professor')
            ->with(['unit', 'teachingAssignments.discipline', 'teachingAssignments.classOffering']);

        // FILTROS
        if ($unit = request('filter_unit')) {
            $query->where('unit_id', $unit);
        }

        if ($course = request('filter_course')) {
            $query->whereHas('teachingAssignments.discipline', function ($q) use ($course) {
                $q->where('course_id', $course);
            });
        }

        if ($offering = request('filter_offering')) {
            $query->whereHas('teachingAssignments.classOffering', function ($q) use ($offering) {
                $q->where('id', $offering);
            });
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('teachers-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0)
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('name')->title('Nome'),
            Column::make('email')->title('Email'),

            Column::computed('unit')
                ->title('Unidade')
                ->orderable(false)
                ->searchable(false),

            Column::computed('disciplines_count')
                ->title('Disciplinas Ativas')
                ->addClass('text-center'),

            Column::computed('actions')
                ->title('Ações')
                ->exportable(false)
                ->printable(false)
                ->width(150)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Professores_' . date('YmdHis');
    }
}
