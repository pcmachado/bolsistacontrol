<?php

namespace App\DataTables;

use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class HomologationsDataTable extends DataTable
{
    protected $filters = [];

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    public function dataTable(QueryBuilder $query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', fn($row) =>
                '<input type="checkbox" name="records[]" value="'.$row->id.'">'
            )
            ->editColumn('date', fn($row) => $row->date->format('d/m/Y'))
            ->addColumn('scholarship_holder', fn($row) =>
                $row->scholarshipHolder?->user?->name ?? 'Bolsista não encontrado'
            )
            ->addColumn('unit', fn($row) =>
                $row->scholarshipHolder?->unit?->name ?? '-'
            )
            ->addColumn('hours', fn($row) => $row->formattedDuration())
            ->addColumn('status_label', function ($row) {
                return match ($row->status) {
                    'submitted' => '<span class="badge bg-info">Enviado</span>',
                    default     => ucfirst($row->status),
                };
            })
            ->addColumn('actions', fn($row) =>
                view('admin.homologations.partials.actions', compact('row'))->render()
            )
            ->rawColumns(['checkbox','status_label','actions']);
    }

    public function query(AttendanceRecord $model)
    {
        $query = $model->newQuery()
            ->with(['scholarshipHolder.user','scholarshipHolder.unit'])
            ->where('status', AttendanceRecord::STATUS_SUBMITTED); // só os enviados para homologação
        $user = Auth::user();

        // --- regras de visibilidade ---
        if ($user->hasRole('coordenador_adjunto')) {
            $query->whereHas('scholarshipHolder', function ($q) use ($user) {
                $q->where('unit_id', $user->unit_id);
            });
        }

        // --- filtros adicionais ---
        if ($this->filters['unit_id'] ?? false) {
            $query->whereHas('scholarshipHolder', fn($q) =>
                $q->where('unit_id', $this->filters['unit_id'])
            );
        }

        if ($this->filters['scholarship_holder_id'] ?? false) {
            $query->where('scholarship_holder_id', $this->filters['scholarship_holder_id']);
        }

        if ($this->filters['status'] ?? false) {
            $query->where('status', $this->filters['status']);
        }

        if ($this->filters['start_date'] ?? false) {
            $query->whereDate('date', '>=', $this->filters['start_date']);
        }

        if ($this->filters['end_date'] ?? false) {
            $query->whereDate('date', '<=', $this->filters['end_date']);
        }

        if ($this->filters['month'] ?? false) {
            $query->whereMonth('date', substr($this->filters['month'], 5, 2))
                ->whereYear('date', substr($this->filters['month'], 0, 4));
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('homologations-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'desc')
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::computed('checkbox')
                ->exportable(false)
                ->printable(false)
                ->width(30)
                ->addClass('text-center')
                ->title('<input type="checkbox" id="select-all">'),
            Column::make('scholarship_holder')->title('Bolsista'),
            Column::make('unit')->title('Unidade'),
            Column::make('date')->title('Data'),
            Column::make('hours')->title('Horas'),
            Column::make('status_label')->title('Status'),
            Column::computed('actions')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center')
                ->title('Ações'),
        ];
    }
}
