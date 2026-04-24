<?php

namespace App\DataTables;

use App\Models\Payment;
use App\Services\VisibilityService;
use App\Support\Traits\PaymentFilters;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PaymentDataTable extends DataTable
{
    public string $mode = 'default';
    
    protected array $filters = [];

    use PaymentFilters;

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('scholarship_holder', fn ($payment) =>
                $payment->scholarshipHolder?->user?->name ?? '-'
            )
            ->addColumn('unit', fn ($payment) =>
                $payment->unit?->name ?? '-'
            )
            ->addColumn('period', fn ($payment) =>
                str_pad($payment->month, 2, '0', STR_PAD_LEFT) . '/' . $payment->year
            )
            ->editColumn('amount', fn ($payment) =>
                number_format($payment->amount, 2, ',', '.')
            )
            ->addColumn('status_label', fn ($payment) =>
                view('admin.payments.partials.status-badge', ['payment' => $payment])->render()
            )
            ->addColumn('actions', fn ($payment) =>
                view('admin.payments.partials.actions', compact('payment'))->render()
            )
            ->rawColumns(['status_label', 'actions']);
    }

    public function query(Payment $model): Builder
    {
        $user = Auth::user();

        $query = $model->newQuery()
            ->with(['scholarshipHolder.user', 'project', 'unit']);

        /*
        |--------------------------------------------------------------------------
        | VISIBILIDADE CENTRALIZADA
        |--------------------------------------------------------------------------
        */

        $context = $this->mode === 'my' ? 'self' : 'admin';

        $query = app(VisibilityService::class)->apply($query, $user, $context);

        $query = $this->applyPaymentFilters($query, request());

        return $query->latest();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('payments-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->responsive(true);
    }

    protected function getColumns(): array
    {
        return [
            ['data' => 'scholarship_holder', 'title' => 'Bolsista'],
            ['data' => 'unit', 'title' => 'Unidade'],
            ['data' => 'period', 'title' => 'Período'],
            ['data' => 'amount', 'title' => 'Valor (R$)'],
            [
                'data' => 'status_label',
                'title' => 'Status',
                'orderable' => false,
                'searchable' => false
            ],
            [
                'data' => 'actions',
                'title' => 'Ações',
                'orderable' => false,
                'searchable' => false
            ],
        ];
    }
}
