<?php

namespace App\DataTables;

use App\Models\AttendanceRecord;
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
        ->editColumn('date', fn($record) => $record->date->format('d/m/Y'))
        ->addColumn('scholarship_holder.name', function ($record) {
            if (Auth::user()->scholarshipHolder && Auth::user()->scholarshipHolder->id === $record->scholarship_holder_id) {
                return ($record->scholarshipHolder->name ?? 'Bolsista não encontrado') . ' <span class="badge bg-secondary">Meu Registro</span>';
            }
            return $record->scholarshipHolder->name ?? 'Bolsista não encontrado';
        })
        ->addColumn('duration', fn($record) => $record->formattedDuration())
        ->addColumn('status_label', function ($record) {
            return match ($record->status) {
                'draft'    => '<span class="badge bg-secondary">Rascunho</span>',
                'submitted'=> '<span class="badge bg-info">Enviado</span>',
                'approved' => '<span class="badge bg-success">Homologado</span>',
                'rejected' => '<span class="badge bg-danger">Rejeitado</span>',
                default    => ucfirst($record->status),
            };
        })
        ->addColumn('actions', function ($record) {
            if ($this->mode === 'homologation') {
                // Modo homologação: ações para coordenadores
                return view('attendance.homologation.partials.actions', compact('record'))->render();
            }
            return view('attendance.partials.actions', compact('record'))->render();
        })
        ->setRowId('id')
        ->rawColumns(['actions', 'scholarship_holder.name', 'status_label']);
}


    public function query(AttendanceRecord $model)
    {
        $query = $model->newQuery()->with(['scholarshipHolder.user']);
        $user = Auth::user();
        // --- LÓGICA DE VISIBILIDADE ---
        switch ($this->mode) {
            case 'approved':
                $query->where('status', 'approved');
                break;

            case 'pending':
                $query->where('status', 'pending');
                break;

            case 'rejected':
                $query->where('status', 'rejected');
                break;

            case 'late':
                $query->late(); // usa o scope que criamos
                break;

            case 'homologation':
                $query->where('status', AttendanceRecord::STATUS_PENDING);
                break;

            default:
                // Admin e Coordenador Geral veem todos os registos.
                if ($user->hasRole(['admin', 'coordenador_geral'])) {
                    // Não aplica filtro, visão total.
                }
                // Coordenador Adjunto vê os seus próprios registos E os registos dos bolsistas da(s) sua(s) unidade(s).
                else if ($user->hasRole('coordenador_adjunto')) {
                    // Pega os IDs das unidades do coordenador.
                    $coordinatorUnitIds = $user->units()->pluck('units.id');

                    $query->where(function ($q) use ($user, $coordinatorUnitIds) {
                        // 1. Inclui os próprios registos do coordenador.
                        if ($user->scholarshipHolder) {
                            $q->where('scholarship_holder_id', $user->scholarshipHolder->id);
                        }

                        // 2. OU inclui os registos de bolsistas que pertencem às suas unidades.
                        $q->orWhereHas('scholarshipHolder', function ($subQuery) use ($coordinatorUnitIds) {
                            $subQuery->whereHas('units', function ($unitQuery) use ($coordinatorUnitIds) {
                                $unitQuery->whereIn('units.id', $coordinatorUnitIds);
                            });
                        });
                    });
                }
                // Bolsista comum vê apenas os seus próprios registos.
                else if ($user->scholarshipHolder) {
                    $query->where('scholarship_holder_id', $user->scholarshipHolder->id);
                }
                // Se não for nenhum dos acima (ex: um utilizador sem papel de bolsista), não mostra nada.
                else {
                    $query->whereRaw('1 = 0');
                }
                break;
        }
        // --- FIM DA LÓGICA DE VISIBILIDADE ---

        // --- APLICAÇÃO DOS FILTROS DA VIEW ---
        if (!empty($this->filters['project_id'])) {
            $query->whereHas('scholarshipHolder', function ($q) {
                $q->where('project_id', $this->filters['project_id']);
            });
        }
        if (!empty($this->filters['unit_id'])) {
            $query->whereHas('scholarshipHolder.units', function ($q) {
                $q->where('units.id', $this->filters['unit_id']);
            });
        }
        if (!empty($this->filters['scholarship_holder_id'])) {
            $query->where('scholarship_holder_id', $this->filters['scholarship_holder_id']);
        }
        if (!empty($this->filters['month'])) {
            $query->whereYear('date', '=', substr($this->filters['month'], 0, 4))
                  ->whereMonth('date', '=', substr($this->filters['month'], 5, 2));
        }
        // --- FIM DOS FILTROS ---
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
                        'language' => ['url' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'],
                    ])
                    ->buttons([
                        Button::make('excel')->className('btn btn-success')->text('Excel'),
                        Button::make('csv')->className('btn btn-info')->text('CSV'),
                        Button::make('print')->className('btn btn-secondary')->text('Imprimir'),
                        Button::make('reset')->className('btn btn-dark')->text('Recarregar'),
                    ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('date')->title('Data'),
            Column::make('scholarship_holder.name')->title('Bolsista')->data('scholarship_holder.name'),
            Column::make('start_time')->title('Entrada'),
            Column::make('end_time')->title('Saída'),
            Column::make('hours')->title('Horas'),
            Column::make('status')->title('Status'),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center')
                  ->title('Ações'),
        ];
    }

    protected function filename(): string
    {
        return 'AttendanceRecords_' . date('YmdHis');
    }
}
