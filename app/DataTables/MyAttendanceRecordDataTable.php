<?php

namespace App\DataTables;

use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\Auth;

class MyAttendanceRecordDataTable extends BaseAttendanceDataTable
{
    public function dataTable($query)
    {
        return $this->buildDataTable($query);
    }

    public function query(AttendanceRecord $model)
    {
        $user = Auth::user();

        $query = $model->newQuery()
            ->with(['submission'])
            ->where('scholarship_holder_id', $user->scholarshipHolder->id);

        return $this->applyFilters($query)
            ->latest('date');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('my-attendance-table')
            ->minifiedAjax()
            ->responsive(true)
            ->columns($this->getColumns());
    }
}