<?php

namespace App\DataTables;

use App\Models\AttendanceRecord;

class AttendanceReportDataTable extends BaseAttendanceDataTable
{
    public function query()
    {
        return AttendanceRecord::with([
            'submission',
            'project',
            'scholarshipHolder.user',
            'scholarshipHolder.unit',
        ]);
    }

    public function dataTable($query)
    {
        return $this->buildDataTable($query)
            ->addColumn('name', fn ($row) =>
                $row->scholarshipHolder?->user?->name ?? '-'
            )
            ->addColumn('unit', fn ($row) =>
                $row->scholarshipHolder?->unit?->name ?? '-'
            );
    }

    protected function getColumns(): array
    {
        return [
            ['data' => 'name', 'title' => 'Bolsista'],
            ['data' => 'unit', 'title' => 'Unidade'],
            ...parent::getColumns(),
        ];
    }
}