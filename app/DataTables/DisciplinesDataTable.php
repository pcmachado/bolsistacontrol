<?php

namespace App\DataTables;

use App\Models\Discipline;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;

class DisciplinesDataTable extends DataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('course', fn($row) => $row->course->name)
            ->editColumn('workload', fn($row) => $row->workload ?? '-')
            ->editColumn('sequence_order', fn($row) => $row->sequence_order ?? '-')
            ->addColumn('actions', function ($row) {
                return view('admin.disciplines.partials.actions', compact('row'));
            })
            ->rawColumns(['actions'])
            ->setRowId('id');
    }

    public function query(Discipline $model)
    {
        $query = $model->newQuery()->with('course');

        // FILTRO POR CURSO (opcional)
        if (request()->filled('filter_course')) {
            $query->where('course_id', request('filter_course'));
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('disciplines-table')
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
            Column::make('name')->title('Disciplina'),
            Column::computed('course')->title('Curso'),
            Column::computed('workload')->title('Carga Horária'),
            Column::computed('sequence_order')->title('Ordem'),
            Column::computed('actions')
                ->title('Ações')
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
