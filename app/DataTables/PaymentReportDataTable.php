<?php

namespace App\DataTables;

use App\Models\Payment;
use Yajra\DataTables\EloquentDataTable;
use App\DataTables\BaseDataTable;
use App\Services\FinancialReportService;

class PaymentReportDataTable extends BaseDataTable
{
    protected array $filters = [];

    public function setFilters(array $filters): static
    {
        $this->filters = $filters;
        return $this;
    }

    public function query(Payment $model)
    {
        return app(FinancialReportService::class)
            ->query($this->filters)
            ->select('payments.*');
    }

    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('scholarship_holder', fn($payment) =>
                $payment->scholarshipHolder?->user?->name
            )
            ->addColumn('project', fn($payment) =>
                $payment->project?->name
            )
            ->addColumn('unit', fn($payment) =>
                $payment->unit?->name
            )
            ->addColumn('period', fn($payment) =>
                str_pad($payment->month, 2, '0', STR_PAD_LEFT) . '/' . $payment->year
            )
            ->addColumn('amount', fn($payment) =>
                number_format($payment->amount, 2, ',', '.')
            )
            ->addColumn('status_badge', function ($payment) {

                $colors = [
                    'sent_to_payment' => 'warning',
                    'paid'            => 'primary',
                    'confirmed'       => 'success',
                ];

                return '<span class="badge bg-' . ($colors[$payment->status] ?? 'secondary') . '">'
                    . ucfirst(str_replace('_', ' ', $payment->status))
                    . '</span>';
            })
            ->addColumn('actions', function ($payment) {
                return view('admin.payments.partials.actions', compact('payment'));
            })
            ->rawColumns(['status_badge', 'actions']);
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('payments-report-table')
            ->columns([
                ['data' => 'scholarship_holder', 'title' => 'Bolsista'],
                ['data' => 'project', 'title' => 'Projeto'],
                ['data' => 'unit', 'title' => 'Unidade'],
                ['data' => 'period', 'title' => 'Período'],
                ['data' => 'amount', 'title' => 'Valor (R$)'],
                ['data' => 'status_badge', 'title' => 'Status', 'orderable' => false, 'searchable' => false],
                ['data' => 'actions', 'title' => 'Ações', 'orderable' => false, 'searchable' => false, 'width' => '150px'],
            ])
            ->minifiedAjax()
            ->orderBy(3)
            ->parameters($this->defaultParameters());
    }
}