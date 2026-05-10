<?php

namespace App\DataTables;

use App\Models\AttendanceRecord;
use App\DataTables\BaseDataTable;

class AttendanceReportDataTable extends BaseDataTable
{
    public function query()
    {
        return AttendanceRecord::with([
            'scholarshipHolder.user',
            'scholarshipHolder.unit'
        ]);
    }

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('name', function ($row) {
                return $row->scholarshipHolder->user->name;
            })
            ->addColumn('unit', function ($row) {
                return $row->scholarshipHolder->unit->name ?? '';
            })
            ->addColumn('hours', fn($row) => $row->hours)
            ->addColumn('date', fn($row) => $row->date);
    }
}