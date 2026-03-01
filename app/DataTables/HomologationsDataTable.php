<?php

namespace App\DataTables;

use App\Models\AttendanceSubmission;
use App\Services\VisibilityService;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class HomologationsDataTable extends DataTable
{
    protected array $filters = [];

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function dataTable(QueryBuilder $query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('checkbox', fn ($row) =>
                '<input type="checkbox" name="submissions[]" value="'.$row->id.'">'
            )
            ->addColumn('bolsista', fn ($row) =>
                $row->scholarshipHolder?->user?->name ?? '-'
            )
            ->addColumn('project', fn ($row) =>
                $row->scholarshipHolder?->projects?->first()?->name ?? '-'
            )
            ->addColumn('period', fn ($row) =>
                sprintf('%02d/%d', $row->month, $row->year)
            )
            ->editColumn('records_count', fn ($row) => (int) $row->records_count)
            ->addColumn('status_label', function ($row) {
                return match ($row->status) {
                    AttendanceSubmission::STATUS_SUBMITTED => '<span class="badge bg-info">Enviado</span>',
                    AttendanceSubmission::STATUS_APPROVED => '<span class="badge bg-success">Homologado</span>',
                    AttendanceSubmission::STATUS_REJECTED => '<span class="badge bg-danger">Rejeitado</span>',
                    default => ucfirst((string) $row->status),
                };
            })
            ->editColumn('submitted_at', fn ($row) =>
                $row->submitted_at?->format('d/m/Y H:i') ?? '-'
            )
            ->addColumn('actions', fn ($row) =>
                view('admin.homologations.partials.actions', compact('row'))->render()
            )
            ->rawColumns(['checkbox', 'status_label', 'actions']);
    }

    public function query(AttendanceSubmission $model): QueryBuilder
    {
        $user = Auth::user();

        $query = $model->newQuery()
            ->with([
                'scholarshipHolder.user',
                'scholarshipHolder.unit',
                'scholarshipHolder.projects',
            ])
            ->withCount('attendanceRecords')
            ->whereIn('status', [
                AttendanceSubmission::STATUS_SUBMITTED,
                AttendanceSubmission::STATUS_APPROVED,
                AttendanceSubmission::STATUS_REJECTED,
            ]);

        $query = app(VisibilityService::class)
            ->apply($query, $user, 'admin');

        $monthFilter = $this->filters['month'] ?? now()->format('Y-m');

        if (! preg_match('/^\d{4}-\d{2}$/', $monthFilter)) {
            throw new InvalidArgumentException('Formato de mes invalido.');
        }

        [$year, $month] = explode('-', $monthFilter);

        $query->where('year', (int) $year)
            ->where('month', (int) $month);

        if (! empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            $query->where('status', $this->filters['status']);
        }

        if (! empty($this->filters['unit_id'])) {
            $unitId = (int) $this->filters['unit_id'];

            $query->whereHas('scholarshipHolder', fn ($q) =>
                $q->where('unit_id', $unitId)
            );
        }

        if (! empty($this->filters['project_id'])) {
            $projectId = (int) $this->filters['project_id'];

            $query->whereHas('scholarshipHolder.projects', fn ($q) =>
                $q->where('projects.id', $projectId)
            );
        }

        if (! empty($this->filters['role'])) {
            $role = $this->filters['role'];

            $query->whereHas('scholarshipHolder.classOfferings', fn ($q) =>
                $q->wherePivot('role', $role)
            );
        }

        if (! empty($this->filters['scholarship_holder_id'])) {
            $query->where('scholarship_holder_id', (int) $this->filters['scholarship_holder_id']);
        }

        return $query->latest('submitted_at');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('homologations-table')
            ->columns($this->getColumns())
            ->minifiedAjax(request()->fullUrl())
            ->orderBy(6, 'desc')
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
            Column::computed('bolsista')->title('Bolsista'),
            Column::computed('project')->title('Projeto'),
            Column::computed('period')->title('Periodo'),
            Column::make('records_count')->title('Registros'),
            Column::computed('status_label')->title('Status'),
            Column::make('submitted_at')->title('Enviado em'),
            Column::computed('actions')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center')
                ->title('Acoes'),
        ];
    }
}
