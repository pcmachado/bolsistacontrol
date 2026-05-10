<?php

namespace App\DataTables;

use App\Models\StudentPayment;
use Yajra\DataTables\EloquentDataTable;
use App\DataTables\BaseDataTable;

class StudentPaymentDataTable extends BaseDataTable
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
            ->addColumn('checkbox', function ($p) {
                return '<input type="checkbox" name="ids[]" value="'.$p->id.'">';
            })
            ->addColumn('student', fn($p) => $p->student->name)
            ->addColumn('class', fn($p) => $p->classOffering->name)
            ->addColumn('period', fn($p) => sprintf('%02d/%s', $p->month, $p->year))
            ->addColumn('amount', fn($p) => 'R$ ' . number_format($p->amount, 2, ',', '.'))
            ->addColumn('status', function ($p) {
                return match ($p->computed_status) {
                    'pending' => '<span class="badge bg-secondary">Pendente</span>',
                    'sent'    => '<span class="badge bg-warning">Enviado</span>',
                    'paid'    => '<span class="badge bg-success">Pago</span>',
                    default   => '-',
                };
            })
            ->addColumn('actions', function ($p) {
                return view('admin.student-payments.partials.actions', compact('p'));
            })
            ->rawColumns(['checkbox', 'status', 'actions']);
    }

    public function query(StudentPayment $model)
    {
        $query = $model->newQuery()
            ->with([
                'student',
                'classOffering',
                'classOffering.unit',
                'classOffering.course',
            ]);

        if (!empty($this->filters['month'])) {
            $query->where('month', $this->filters['month']);
        }

        if (!empty($this->filters['year'])) {
            $query->where('year', $this->filters['year']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['class_id'])) {
            $query->where('class_offering_id', $this->filters['class_id']);
        }

        if (!empty($this->filters['unit_id'])) {
            $query->whereHas('classOffering.unit', function ($q) {
                $q->where('id', $this->filters['unit_id']);
            });
        }

        if (!empty($this->filters['course_id'])) {
            $query->whereHas('classOffering.course', function ($q) {
                $q->where('id', $this->filters['course_id']);
            });
        }

        return $query->latest();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('student-payments-table')
            ->minifiedAjax()
            ->columns([
                ['data' => 'checkbox', 'title' => '<input type="checkbox" id="select-all">', 'orderable' => false, 'searchable' => false],
                ['data' => 'student', 'title' => 'Aluno'],
                ['data' => 'class', 'title' => 'Turma'],
                ['data' => 'period', 'title' => 'Período'],
                ['data' => 'amount', 'title' => 'Valor'],
                ['data' => 'status', 'title' => 'Status'],
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