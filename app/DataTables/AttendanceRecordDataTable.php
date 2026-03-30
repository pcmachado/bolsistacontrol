<?php

namespace App\DataTables;

use App\Models\AttendanceRecord;
use App\Services\VisibilityService;
use Illuminate\Support\Facades\Auth;

class AttendanceRecordDataTable extends BaseAttendanceDataTable
{
    public function dataTable($query)
    {
        return $this->buildDataTable($query)
            ->addColumn('user', fn ($r) =>
                $r->scholarshipHolder?->user?->name ?? '-'
            );
    }

    public function query(AttendanceRecord $model)
    {
        $user = Auth::user();

        $query = $model->newQuery()
            ->with(['scholarshipHolder.user', 'submission']);

        $query = app(VisibilityService::class)
            ->apply($query, $user, 'admin');

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