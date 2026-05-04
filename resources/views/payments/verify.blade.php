@extends('layouts.guest')

@section('title', 'Verificar Recibo')

@section('content')
<div class="container">

    <h3 class="mb-4">🔍 Verificação de Recibo</h3>

    <form method="POST" action="{{ route('payments.verify') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Código de verificação</label>
            <input type="text" name="hash" class="form-control"
                   value="{{ old('hash') }}" required>
        </div>

        <button class="btn btn-primary">
            Verificar
        </button>
    </form>

    @isset($searched)
        <hr>

        @if($payment)
            <div class="alert alert-success mt-4">
                ✅ Recibo válido
            </div>

            <div class="card">
                <div class="card-body">
                    <strong>Bolsista:</strong> {{ $payment->scholarshipHolder->user->name }}<br>
                    <strong>Período:</strong> {{ $payment->periodLabel() }}<br>
                    <strong>Valor:</strong> R$ {{ number_format($payment->amount, 2, ',', '.') }}<br>
                    <strong>Status:</strong> Confirmado
                </div>
            </div>
        @else
            <div class="alert alert-danger mt-4">
                ❌ Recibo inválido ou não encontrado
            </div>
        @endif
    @endisset

</div>
@endsection