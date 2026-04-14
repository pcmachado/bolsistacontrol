<?php

namespace App\DataTables;

use App\Models\Payment;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use App\Services\FinancialReportService;

class PaymentReportDataTable extends DataTable
{
    protected array $filters = [];

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    public function query(Payment $model)
    {
        $service = app(FinancialReportService::class);

        return $service->get($this->filters)->select('payments.*');
    }

    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('bolsista', fn($payment) => $payment->scholarshipHolder?->user?->name)
            ->addColumn('projeto', fn($payment) => $payment->project?->name)
            ->addColumn('unidade', fn($payment) => $payment->unit?->name)
            ->addColumn('periodo', fn($payment) => str_pad($payment->month,2,'0',STR_PAD_LEFT).'/'.$payment->year)
            ->addColumn('valor', fn($payment) => number_format($payment->amount,2,',','.'))
            ->addColumn('status_badge', function ($payment) {
                $colors = [
                    'sent_to_payment' => 'warning',
                    'paid'            => 'primary',
                    'confirmed'       => 'success',
                ];

                return '<span class="badge bg-'.($colors[$payment->status] ?? 'secondary').'">'
                        . ucfirst(str_replace('_',' ', $payment->status))
                        . '</span>';
            })
            ->addColumn('actions', function ($payment) {
                return view('admin.payments.partials.actions', compact('payment'));
            })
            ->rawColumns(['status_badge','actions']);
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('payments-report-table')
            ->columns([
                ['data' => 'bolsista', 'title' => 'Bolsista'],
                ['data' => 'projeto', 'title' => 'Projeto'],
                ['data' => 'unidade', 'title' => 'Unidade'],
                ['data' => 'periodo', 'title' => 'Período'],
                ['data' => 'valor', 'title' => 'Valor (R$)'],
                ['data' => 'status_badge', 'title' => 'Status', 'orderable' => false, 'searchable' => false],
                ['data' => 'actions', 'title' => 'Ações', 'orderable' => false, 'searchable' => false, 'width' => '150px'],
            ])
            ->minifiedAjax()
            ->orderBy(3);
    }
}
