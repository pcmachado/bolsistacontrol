<?php

namespace App\DataTables;

use App\Models\Student;
use App\Models\ClassOffering;
use Yajra\DataTables\EloquentDataTable;
use App\DataTables\BaseDataTable;

class ClassOfferingStudentDataTable extends BaseDataTable
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
                return view('admin.class-offerings.students.partials.actions', ['student' => $s, 'classId' => $this->classId]);
            })
            ->rawColumns(['actions']);
    }

    public function query(Student $model)
    {
        return ClassOffering::findOrFail($this->classId)
        ->students()
        ->select('students.*');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('class-students-table')
            ->minifiedAjax()
            ->parameters($this->defaultParameters())
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