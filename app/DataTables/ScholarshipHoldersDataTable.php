<?php

namespace App\DataTables;

use App\Models\ScholarshipHolder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\DataTables\BaseDataTable;

class ScholarshipHoldersDataTable extends BaseDataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addColumn('user', function ($scholarshipHolder) {
                return $scholarshipHolder->user->name ?? 'N/A';
            })
            ->editColumn('created_at', function ($scholarshipHolder) {
                return formatDate($scholarshipHolder->created_at);
            })
            ->editColumn('updated_at', function ($scholarshipHolder) {
                return formatDate($scholarshipHolder->updated_at);
            })
            ->addColumn('unit', function ($scholarshipHolder) {
                return $scholarshipHolder->unit->name ?? 'N/A';
            })
            ->addColumn('actions', 'admin.scholarship_holders.partials.actions')
            ->rawColumns(['actions']);
    }

    public function query(ScholarshipHolder $model)
    {
        $user = Auth::user();

        $query = $model->newQuery()
            ->with(['user', 'unit']);

        // 🔒 aplicar visibilidade por instituição
        if (! $user->hasRole('superadmin')) {
            $query->whereHas('unit', function ($q) use ($user) {
                $q->whereIn('institution_id', $user->activeInstitutionIds());
            });
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('scholarship_holders-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0, 'asc')
            ->parameters($this->defaultParameters())
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
            Column::make('email')->title('E-mail'),
            Column::make('user')->title('Usuário')->orderable(false)->searchable(false), // Adiciona a coluna
            Column::make('unit')->title('Unidade')->orderable(false)->searchable(false), // Adiciona a coluna de unidades
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
        return 'ScholarshipHolders_'.date('YmdHis');
    }
}
