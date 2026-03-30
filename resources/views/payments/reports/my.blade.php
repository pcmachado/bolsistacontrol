@extends('layouts.pdf')

@section('title', 'Relatório de Pagamentos')

@section('header-extra')

<div style="margin-top: 5px;">
    <h3>RELATÓRIO DE PAGAMENTOS</h3>
    <h5>
        @if(request('month'))
            {{ \Carbon\Carbon::parse(request('month'))->format('m/Y') }}
        @elseif(request('year'))
            Ano {{ request('year') }}
        @else
            Todos os períodos
        @endif
    </h5>
</div>

@endsection

@section('content')

<h5>1. Pagamentos</h5>

<table class="no-break">
<thead>
<tr>
    <th>Bolsista</th>
    <th>Unidade</th>
    <th>Período</th>
    <th>Valor (R$)</th>
    <th>Status</th>
</tr>
</thead>
<tbody>

@forelse($payments as $payment)
<tr>
    <td>{{ $payment->scholarshipHolder?->user?->name }}</td>
    <td>{{ $payment->unit?->name }}</td>
    <td>{{ str_pad($payment->month, 2, '0', STR_PAD_LEFT) }}/{{ $payment->year }}</td>
    <td>R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
    <td>{{ ucfirst($payment->status) }}</td>
</tr>
@empty
<tr>
    <td colspan="5">Nenhum pagamento encontrado.</td>
</tr>
@endforelse

</tbody>
</table>

<h5>2. Total geral</h5>

<table class="no-break">
<tr>
    <td width="70%"><strong>Total a pagar</strong></td>
    <td><strong>R$ {{ number_format($total, 2, ',', '.') }}</strong></td>
</tr>
</table>

@endsection