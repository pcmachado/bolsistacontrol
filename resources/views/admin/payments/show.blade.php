@extends('layouts.app')

@section('title', 'Detalhes do Pagamento')

@section('content')
<div class="container">
    <h3 class="mb-4">
        <i class="bi bi-cash-coin me-2"></i>
        Detalhes do Pagamento
    </h3>

    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="card-title">
                {{ $payment->scholarshipHolder->name }}
                <small class="text-muted">({{ $payment->scholarshipHolder->user->email }})</small>
            </h5>

            <p><strong>Período:</strong> {{ $payment->month }}/{{ $payment->year }}</p>
            <p><strong>Valor:</strong> R$ {{ number_format($payment->amount, 2, ',', '.') }}</p>
            <p><strong>Status:</strong>
                @php
                    $statusColors = [
                        'sent_to_payment' => 'warning',
                        'paid' => 'success',
                        'confirmed' => 'primary',
                    ];
                @endphp
                <span class="badge bg-{{ $statusColors[$payment->status] ?? 'secondary' }}">
                    {{ ucfirst(str_replace('_',' ', $payment->status)) }}
                </span>
            </p>

            {{-- Ações --}}
            @include('admin.payments.partials.actions', ['payment' => $payment])

        </div>
    </div>
@endsection