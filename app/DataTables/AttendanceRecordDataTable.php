<?php

namespace App\DataTables;

use App\Models\AttendanceRecord;
use App\Services\VisibilityService;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Services\DataTable;

class AttendanceRecordDataTable extends BaseAttendanceDataTable
{
    protected array $filters = [];

    public string $mode = 'admin';

    public function dataTable($query)
    {
        return $this->buildDataTable($query)
            ->addColumn('user', fn ($r) =>
                $r->scholarshipHolder?->user?->name ?? '-'
            );
    }

    public function setMode(string $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

    public function query(AttendanceRecord $model)
    {
        $user = Auth::user();

        $query = $model->newQuery()
            ->with(['scholarshipHolder.user', 'submission']);

        $visibility = app(VisibilityService::class);
        
        $context = $this->mode === 'self' ? 'self' : 'admin';

        $query = $visibility->apply($query, $user, $context);

        return $this->applyFilters($query)
            ->latest('date');
    }

    protected function getColumns(): array
    {
        return [
            ['data' => 'user', 'title' => 'Bolsista'],
            ...parent::getColumns(),
        ];
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('attendance-table')
            ->minifiedAjax()
            ->responsive(true)
            ->columns($this->getColumns());
    }
}