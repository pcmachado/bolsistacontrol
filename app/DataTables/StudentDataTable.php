<?php
namespace App\DataTables;

use App\Models\Student;
use Yajra\DataTables\EloquentDataTable;
use App\DataTables\BaseDataTable;

class StudentDataTable extends BaseDataTable
{
    protected array $filters = [];

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('class', fn($s) => $s->classOfferings->pluck('name')->implode(', ') ?: '-')
            ->addColumn('payment', fn($s) => strtoupper($s->payment_type))
            ->addColumn('actions', function ($s) {
                return view('admin.students.partials.actions', compact('s'));
            })
            ->rawColumns(['actions']);
    }

    public function query(Student $model)
    {
        $query = $model->newQuery();
            // ->with('classOfferings');

        if (!empty($this->filters['class_offering_id'])) {
            $query->whereHas('classOfferings', function($q) {
                $q->where('class_offerings.id', $this->filters['class_offering_id']);
            });
        }

        return $query->latest();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('students-table')
            ->minifiedAjax()
            ->columns([
                ['data' => 'name', 'title' => 'Nome'],
                ['data' => 'class', 'title' => 'Turma'],
                ['data' => 'payment', 'title' => 'Pagamento'],
                [
                    'data' => 'actions',
                    'title' => 'Ações',
                    'orderable' => false,
                    'searchable' => false,
                ],
            ])
            ->parameters($this->defaultParameters());
    }
}