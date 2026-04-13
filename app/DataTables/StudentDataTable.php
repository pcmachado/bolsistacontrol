<?php
namespace App\DataTables;

use App\Models\Student;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class StudentDataTable extends DataTable
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
            ->addColumn('class', fn($s) => $s->classOffering->name ?? '-')
            ->addColumn('payment', fn($s) => strtoupper($s->payment_type))
            ->addColumn('actions', function ($s) {
                return view('students.partials.actions', compact('s'));
            })
            ->rawColumns(['actions']);
    }

    public function query(Student $model)
    {
        $query = $model->newQuery()
            ->with('classOffering');

        // 🔎 filtro por turma
        if (!empty($this->filters['class_offering_id'])) {
            $query->where('class_offering_id', $this->filters['class_offering_id']);
        }

        return $query->latest();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('students-table')
            ->minifiedAjax()
            ->responsive(true)
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
            ]);
    }
}