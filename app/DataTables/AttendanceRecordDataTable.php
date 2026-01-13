<?php

namespace App\DataTables;

use App\Models\AttendanceRecord;
use App\Services\AttendanceVisibilityService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;


class AttendanceRecordDataTable extends DataTable
{
    public $mode = 'default';
    protected $filters = [];

    /**
     * Seta os filtros a serem aplicados na query.
     */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    public function dataTable(QueryBuilder $query)
    {
        return (new EloquentDataTable($query))
            // Coluna de checkbox para seleção em lote
            ->addColumn('checkbox', function ($attendanceRecord) {
                return '<input type="checkbox" name="records[]" value="'.$attendanceRecord->id.'">';
            })

            // Data formatada
            ->editColumn('date', fn($attendanceRecord) => $attendanceRecord->date->format('d/m/Y'))

            // Nome do bolsista
            ->addColumn('scholarship_holder.name', function ($attendanceRecord) {
                if (Auth::user()->scholarshipHolder && Auth::user()->scholarshipHolder->id === $attendanceRecord->scholarship_holder_id) {
                    return ($attendanceRecord->scholarshipHolder->name ?? 'Bolsista não encontrado') .
                        ' <span class="badge bg-secondary">Meu Registro</span>';
                }
                return $attendanceRecord->scholarshipHolder->name ?? 'Bolsista não encontrado';
            })

            // Duração formatada
            ->addColumn('duration', fn($attendanceRecord) => $attendanceRecord->formattedDuration())

            // Status com badge
            ->addColumn('status_label', function ($attendanceRecord) {
                return match ($attendanceRecord->status) {
                    'draft'    => '<span class="badge bg-secondary">Rascunho</span>',
                    'submitted'=> '<span class="badge bg-info">Enviado</span>',
                    'approved' => '<span class="badge bg-success">Homologado</span>',
                    'rejected' => '<span class="badge bg-danger">Rejeitado</span>',
                    'late'     => '<span class="badge bg-warning text-dark">Atrasado</span>',
                    default    => ucfirst($attendanceRecord->status),
                };
            })

            // Ações (parciais diferentes conforme o modo)
            ->addColumn('actions', function ($attendanceRecord) {
                if ($this->mode === 'homologation') {
                    return view('admin.homologations.partials.actions', compact('attendanceRecord'))->render();
                }
                return view('attendance.partials.actions', compact('attendanceRecord'))->render();
            })

            ->setRowId('id')
            ->rawColumns(['checkbox','actions','scholarship_holder.name','status_label']);
    }

    public function query(AttendanceRecord $model)
    {
        $user  = Auth::user();

        $query = $model->newQuery()
            ->with(['scholarshipHolder.user', 'scholarshipHolder.unit']);

        // aplica regras de visibilidade
        app(AttendanceVisibilityService::class)->apply(
            $query,
            $user,
            $this->mode
        );

        // --- INÍCIO DOS FILTROS ---
        if (!empty($this->filters['unit_id'])) {
            $query->whereHas('scholarshipHolder', function ($q) {
                $q->where('unit_id', $this->filters['unit_id']);
            });
        }

        if (!empty($this->filters['scholarship_holder_id'])) {
            $query->where('scholarship_holder_id', $this->filters['scholarship_holder_id']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // período
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('date', '>=', $this->filters['start_date']);
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereDate('date', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['month'])) {
            $query->whereYear('date', substr($this->filters['month'], 0, 4))
                ->whereMonth('date', substr($this->filters['month'], 5, 2));
        }

        if (!empty($this->filters['year'])) {
            $query->whereYear('date', $this->filters['year']);
        }
        // --- FIM DOS FILTROS ---
        \Log::info('Attendance DT FINAL SQL', [
        'sql' => $query->toSql(),
        'bindings' => $query->getBindings(),
        'mode' => $this->mode,
        'user' => $user->id,
        'roles' => $user->roles->pluck('name'),
    ]);

        return $query;
    }

    public function html()
    {
        return $this->builder()
                    ->setTableId('attendance_records-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1, 'desc')
                    ->parameters([
                        'responsive' => true,
                        'autoWidth' => false,
                    ])
                    ->buttons([
                        Button::make('excel')->className('btn btn-success')->text('Excel'),
                        Button::make('csv')->className('btn btn-info')->text('CSV'),
                        Button::make('print')->className('btn btn-secondary')->text('Imprimir'),
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

            Column::make('date')->title('Data'),
            Column::make('scholarship_holder.name')->title('Bolsista')->data('scholarship_holder.name'),
            Column::make('duration')->title('Duração'),
            Column::make('status_label')->title('Status')->data('status_label'),

            Column::computed('actions')
                ->exportable(false)
                ->printable(false)
                ->width(80)
                ->addClass('text-center')
                ->title('Ações'),
        ];
    }


    protected function filename(): string
    {
        return 'AttendanceRecords_' . date('YmdHis');
    }
    
}
