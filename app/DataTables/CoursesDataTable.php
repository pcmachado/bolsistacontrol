<?php
namespace App\DataTables;

use App\Models\Course;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class CoursesDataTable extends DataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addColumn('created_at', function ($course) {
                return formatDate($course->created_at);
            })
            ->addColumn('updated_at', function ($course) {
                return formatDate($course->updated_at);
            })
            ->addColumn('projects', function ($course) {
                return $course->projects->pluck('name')->implode('<br>');
            })

            ->addColumn('units', function ($course) {
                return $course->units->pluck('name')->implode('<br>');
            })
            ->addColumn('actions', 'admin.courses.partials.actions') // Usando uma view para as ações
            ->rawColumns(['actions']);
    }

    public function query(Course $model)
    {
        $query = $model->newQuery();
        $user = auth()->user();

        // ADMIN e Coordenador Geral → veem tudo
        if ($user->hasRole(['admin', 'coordenador_geral'])) {
            return $query;
        }

        // Coordenador Adjunto → só cursos da sua unidade
        if ($user->hasRole('coordenador_adjunto')) {
            return $query->whereHas('classOfferings', function ($q) use ($user) {
                $q->where('unit_id', $user->unit_id);
            });
        }

        // Demais usuários → não veem nada
        return $query->whereRaw('1=0');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('courses-table')
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
            Column::make('created_at')->title('Criado Em'),
            Column::make('updated_at')->title('Atualizado Em'),
            Column::computed('actions')
                ->exportable(false)
                ->printable(false)
                ->width(150)
                ->addClass('text-center')
                ->title('Ações'),
        ];
    }

    protected function filename(): string
    {
        return 'Courses_' . date('YmdHis');
    }
}