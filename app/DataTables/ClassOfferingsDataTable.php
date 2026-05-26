<?php

namespace App\DataTables;

use App\Models\ClassOffering;
use App\Services\VisibilityService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\DataTables\BaseDataTable;

class ClassOfferingsDataTable extends BaseDataTable
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
            ->addColumn('course', fn ($row) => $row->course?->name ?? '-')
            ->addColumn('unit', fn ($row) => $row->unit?->name ?? '-')
            ->addColumn('project', fn ($row) => $row->project?->name ?? '-')
            ->addColumn('disciplines_count', fn ($row) => $row->disciplines_count ?? 0)
            ->addColumn('teachers_count', fn ($row) => $row->teachers_count ?? 0)
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                    'planned' => '<span class="badge bg-secondary">Planejado</span>',
                    'ongoing' => '<span class="badge bg-primary">Em andamento</span>',
                    'finished' => '<span class="badge bg-success">Concluido</span>',
                    'cancelled' => '<span class="badge bg-danger">Cancelado</span>',
                    default => (string) $row->status,
                };
            })
            ->addColumn('actions', fn ($row) => view('admin.class-offerings.partials.actions', compact('row')))
            ->rawColumns(['status', 'actions'])
            ->setRowId('id');
    }

    public function query(ClassOffering $model)
    {
        $user = Auth::user();

        $query = $model->newQuery()
            ->with(['course', 'unit', 'project'])
            ->withCount(['disciplines', 'teachers']);

        $query = app(VisibilityService::class)
            ->apply($query, $user, 'admin');

        if ($course = ($this->filters['filter_course'] ?? null)) {
            $query->where('course_id', $course);
        }

        if ($unit = ($this->filters['filter_unit'] ?? null)) {
            $query->where('unit_id', $unit);
        }

        if ($project = ($this->filters['filter_project'] ?? null)) {
            $query->where('project_id', $project);
        }

        if ($status = ($this->filters['filter_status'] ?? null)) {
            $query->where('status', $status);
        }

        if ($year = ($this->filters['filter_year'] ?? null)) {
            $query->where('year', $year);
        }

        if ($semester = ($this->filters['filter_semester'] ?? null)) {
            $query->where('semester', 'like', "%$semester%");
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('class-offerings-table')
            ->columns($this->getColumns())
            ->minifiedAjax(request()->fullUrl())
            ->orderBy(0, 'asc')
            ->parameters($this->defaultParameters())
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
            Column::computed('disciplines_count')->title('Disciplinas')->addClass('text-center'),
            Column::computed('teachers_count')->title('Professores')->addClass('text-center'),
            Column::computed('status')->title('Status'),
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
        return 'Turmas_' . date('YmdHis');
    }
}
