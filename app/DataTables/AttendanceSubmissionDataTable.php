<?php

namespace App\DataTables;

use App\Models\AttendanceSubmission;
use App\Services\VisibilityService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class AttendanceSubmissionDataTable extends DataTable
{
    protected array $filters = [];

    public string $mode = 'admin';

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    public function setMode(string $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('bolsista', fn ($row) =>
                $row->scholarshipHolder?->user?->name ?? '-'
            )

            ->addColumn('period', fn ($row) =>
                sprintf('%02d/%d', $row->month, $row->year)
            )

            ->addColumn('records_count', fn ($row) =>
                $row->records_count
            )

            ->addColumn('status_label', fn ($row) =>
                view('attendance.submissions.partials.status', compact('row'))->render()
            )

            ->addColumn('actions', fn ($row) =>
                view('attendance.submissions.partials.actions', compact('row'))->render()
            )

            ->rawColumns(['status_label', 'actions']);
    }

    public function query(AttendanceSubmission $model)
    {
        $user = Auth::user();

        $query = $model->newQuery()
            ->with([
                'scholarshipHolder.user',
                'scholarshipHolder.unit',
            ])
            ->withCount('attendanceRecords');

        $visibility = app(VisibilityService::class);
        
        $context = $this->mode === 'self' ? 'self' : 'admin';

        $query = $visibility->apply($query, $user, $context);

        if (!empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['month'])) {

            if (! preg_match('/^\d{4}-\d{2}$/', $this->filters['month'])) {
                throw new \InvalidArgumentException('Formato de mês inválido.');
            }

            [$year, $month] = explode('-', $this->filters['month']);
            $query->where('year', $year)
                  ->where('month', $month);
        }

        if (!empty($this->filters['unit_id'])) {

            $unitId = $this->filters['unit_id'];

            $query->whereHas('scholarshipHolder', function ($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            });
        }

        return $query->latest('submitted_at');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('attendance-submissions-table')
            ->minifiedAjax()
            ->responsive(true)
            ->columns([
                ['data' => 'bolsista', 'title' => 'Bolsista'],
                ['data' => 'period', 'title' => 'Período'],
                ['data' => 'records_count', 'title' => 'Registros'],
                ['data' => 'status_label', 'title' => 'Status'],
                [
                    'data'       => 'actions',
                    'title'      => 'Ações',
                    'orderable'  => false,
                    'searchable' => false,
                ],
            ]);
    }
}
