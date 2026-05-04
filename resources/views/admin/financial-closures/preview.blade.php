@extends('layouts.app')

@section('title', 'Prévia do Fechamento')

@section('content')
<div class="container-fluid">

    <h1 class="mb-4">
        Prévia do Fechamento
        <small class="text-muted">
            {{ str_pad($month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}
        </small>
    </h1>

    @foreach($payments as $unitId => $items)
        @php
            $unit = $items->first()->unit;
            $total = $items->sum('amount');
        @endphp

        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $unit?->name }}</strong>
                    <small class="text-muted d-block">
                        {{ $items->count() }} pagamento(s)
                    </small>
                </div>

                <div class="text-end">
                    <strong>
                        R$ {{ number_format($total, 2, ',', '.') }}
                    </strong>
                </div>
            </div>

            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Bolsista</th>
                            <th>Projeto</th>
                            <th>Valor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $payment)
                        <tr>
                            <td>{{ $payment->scholarshipHolder?->user?->name }}</td>
                            <td>{{ $payment->project?->name }}</td>
                            <td>R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                            <td>{{ ucfirst($payment->status) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex gap-2">

                <form method="POST"
                      action="{{ route('admin.financial-closures.store') }}">
                    @csrf
                    <input type="hidden" name="unit_id" value="{{ $unitId }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">

                    <button class="btn btn-success">
                        🔒 Fechar Unidade
                    </button>
                </form>

                <a href="{{ route('admin.payments.reports.monthly', [
                    'unit' => $unitId,
                    'month' => $month,
                    'year' => $year
                ]) }}"
                   class="btn btn-danger">
                    📄 PDF
                </a>

            </div>
        </div>
    @endforeach

</div>
@endsection