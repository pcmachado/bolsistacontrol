@extends('layouts.app')

@section('title', 'Meus Pagamentos')

@section('content')
<div class="container">

    <h1 class="mb-4">Meus Pagamentos</h1>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Período</th>
                <th>Horas</th>
                <th>Valor</th>
                <th>Status</th>
                <th class="text-end">Ação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->periodLabel() }}</td>
                    <td>{{ $payment->total_hours }}</td>
                    <td>R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                    <td>
                        <span class="badge bg-{{ $payment->status === 'paid' ? 'warning' : 'success' }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        @if($payment->isPaid())
                            @can('confirm', $payment)
                                <form method="POST"
                                    action="{{ route('payments.confirm', $payment) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-primary">
                                        Confirmar recebimento
                                    </button>
                                </form>
                            @endcan
                        @elseif($payment->isConfirmed())
                            <a href="{{ route('payments.receipt', $payment) }}"
                               class="btn btn-sm btn-outline-secondary">
                                Ver recibo
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
