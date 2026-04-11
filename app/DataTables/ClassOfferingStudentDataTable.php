<?php

namespace App\DataTables;

use App\Models\Student;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class ClassOfferingStudentDataTable extends DataTable
{
    protected $classId;

    public function forClass($classId)
    {
        $this->classId = $classId;
        return $this;
    }

    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('payment', fn($s) => strtoupper($s->payment_type))
            ->addColumn('actions', function ($s) {
                return view('students.partials.actions', compact('s'));
            })
            ->rawColumns(['actions']);
    }

    public function query(Student $model)
    {
        return $model->newQuery()
            ->where('class_offering_id', $this->classId)
            ->latest();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('class-students-table')
            ->minifiedAjax()
            ->responsive(true)
            ->columns([
                ['data' => 'name', 'title' => 'Nome'],
                ['data' => 'payment', 'title' => 'Pagamento'],
                [
                    'data' => 'actions',
                    'title' => 'Ações',
                    'orderable' => false,
                    'searchable' => false,
                ],
            ]);
    }
}