@extends('layouts.app')

@section('title', 'Prévia do Pagamento em Lote')

@section('content')
<div class="container-fluid">

    <h1 class="mb-3">Prévia do Pagamento</h1>

    <p class="text-muted">
        Unidade: <strong>{{ $unit->name }}</strong> |
        Período: <strong>{{ str_pad($month,2,'0',STR_PAD_LEFT) }}/{{ $year }}</strong>
    </p>

    <form method="POST" action="{{ route('admin.payments.batch.store') }}">
        @csrf

        <input type="hidden" name="unit_id" value="{{ $unit->id }}">
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Bolsista</th>
                    <th>Horas</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($preview as $row)
                    <tr>
                        <td>{{ $row['holder']->user->name }}</td>
                        <td>{{ $row['total_hours'] }}</td>
                        <td>R$ {{ number_format($row['amount'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 d-flex justify-content-between">
            <a href="{{ route('admin.payments.batch.form') }}"
               class="btn btn-outline-secondary">
                ← Voltar
            </a>

            <button class="btn btn-success">
                Confirmar e gerar pagamentos
            </button>
        </div>
    </form>

</div>
@endsection
