<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\DataTables\BaseDataTable;

class TeachersDataTable extends BaseDataTable
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
            ->addColumn('unit', fn ($t) => $t->unit->name ?? '-')

            ->addColumn('disciplines_count', function ($t) {
                return $t->classOfferingDisciplines->count();
            })
            ->addColumn('total_workload', function ($t) {
                return (int) $t->classOfferingDisciplines->sum('workload');
            })
            ->addColumn('disciplines', function ($t) {
                $labels = $t->classOfferingDisciplines
                    ->map(fn ($assignment) => $assignment->discipline?->name)
                    ->filter()
                    ->unique()
                    ->values();

                return $labels->isEmpty() ? '-' : $labels->implode(', ');
            })

            ->addColumn('actions', function ($t) {
                return view('admin.teachers.partials.actions', compact('t'));
            })

            ->rawColumns(['actions'])
            ->setRowId('id');
    }

    public function query(User $model)
    {
        $query = $model->newQuery()
            ->with(['unit', 'classOfferingDisciplines.discipline', 'classOfferingDisciplines.classOffering']);

        $query->where(function ($teacherQuery) {
            $teacherQuery->role('professor')
                ->orWhereHas('scholarshipHolder', function ($holderQuery) {
                    $holderQuery->whereExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('project_scholarship_holder as psh')
                            ->join('positions as p', 'p.id', '=', 'psh.position_id')
                            ->whereColumn('psh.scholarship_holder_id', 'scholarship_holders.id')
                            ->where('p.is_teacher', true);
                    });
                });
        });

        // FILTROS
        if ($unit = ($this->filters['filter_unit'] ?? null)) {
            $query->where('unit_id', $unit);
        }

        if ($course = ($this->filters['filter_course'] ?? null)) {
            $query->whereHas('classOfferingDisciplines.discipline', function ($q) use ($course) {
                $q->where('course_id', $course);
            });
        }

        if ($offering = ($this->filters['filter_offering'] ?? null)) {
            $query->whereHas('classOfferingDisciplines.classOffering', function ($q) use ($offering) {
                $q->where('id', $offering);
            });
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('scholarship_holders-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('admin.teachers.index'))
            ->orderBy(0)
            ->parameters($this->defaultParameters())
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

            Column::computed('total_workload')
                ->title('Carga Horária Total')
                ->addClass('text-center'),

            Column::computed('disciplines')
                ->title('Disciplinas')
                ->orderable(false)
                ->searchable(false),

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
        return 'Professores_'.date('YmdHis');
    }
}
