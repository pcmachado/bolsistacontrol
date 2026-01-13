@extends('layouts.guest')

@section('title', 'Recibo Válido')

@section('content')
<div class="container py-5">
    <h3 class="text-success">✔ Recibo válido</h3>

    <ul class="list-group">
        <li class="list-group-item">
            <strong>Bolsista:</strong> {{ $payment->scholarshipHolder->user->name }}
        </li>
        <li class="list-group-item">
            <strong>Projeto:</strong> {{ $payment->project?->name }}
        </li>
        <li class="list-group-item">
            <strong>Período:</strong> {{ $payment->periodLabel() }}
        </li>
        <li class="list-group-item">
            <strong>Valor:</strong> R$ {{ number_format($payment->amount,2,',','.') }}
        </li>
        <li class="list-group-item">
            <strong>Confirmado em:</strong>
            {{ $payment->confirmed_at->format('d/m/Y') }}
        </li>
    </ul>
</div>
@endsection
