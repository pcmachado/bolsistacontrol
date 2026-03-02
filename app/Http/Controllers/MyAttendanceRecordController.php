<?php

namespace App\Http\Controllers;

use App\DataTables\AttendanceRecordDataTable;
use Illuminate\Http\Request;

class MyAttendanceRecordController extends Controller
{
    public function index(
        Request $request,
        AttendanceRecordDataTable $dataTable
    ) {
        $filters = $request->only(['month', 'status']);

        return $dataTable
            ->setMode('self')
            ->setFilters($filters)
            ->render('attendance.index', [
                'month' => $filters['month'] ?? now()->format('Y-m'),
                'submission' => null, // ou resolver depois
            ]);
    }
}
