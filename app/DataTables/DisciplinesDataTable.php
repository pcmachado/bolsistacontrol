<?php

namespace App\DataTables;

use App\Models\Discipline;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\DataTables\BaseDataTable;
use Illuminate\Support\Facades\Auth;
use App\Services\VisibilityService;

class DisciplinesDataTable extends BaseDataTable
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
            ->editColumn('workload', fn ($row) => $row->workload ?? '-')
            ->editColumn('sequence_order', fn ($row) => $row->sequence_order ?? '-')
            ->addColumn('actions', fn ($row) => view('admin.disciplines.partials.actions', compact('row')))
            ->rawColumns(['actions'])
            ->setRowId('id');
    }

    public function query(Discipline $model)
    {
        $user = Auth::user();

        $query = $model->newQuery()->with('course');

        $query = app(VisibilityService::class)
            ->apply($query, $user, 'admin');

        if (! empty($this->filters['filter_course'])) {
            $query->where('course_id', (int) $this->filters['filter_course']);
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('disciplines-table')
            ->columns($this->getColumns())
            ->minifiedAjax(request()->fullUrl())
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
            Column::make('name')->title('Disciplina'),
            Column::computed('course')->title('Curso'),
            Column::computed('workload')->title('Carga Horaria'),
            Column::computed('sequence_order')->title('Ordem'),
            Column::computed('actions')
                ->title('Acoes')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Disciplinas_' . date('YmdHis');
    }
}
