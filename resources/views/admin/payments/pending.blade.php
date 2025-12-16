@extends('layouts.app')

@section('title', 'Pagamentos Pendentes')

@section('content')
<div class="container">

    <h3 class="mb-4">
        <i class="bi bi-hourglass-split me-2"></i>
        Pagamentos Pendentes
    </h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Bolsista</th>
                        <th>Projeto</th>
                        <th>Unidade</th>
                        <th>Período</th>
                        <th>Horas</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $p)
                        <tr>
                            <td>{{ $p->scholarshipHolder->name }}</td>
                            <td>{{ $p->project->name ?? '-' }}</td>
                            <td>{{ $p->unit->name ?? '-' }}</td>
                            <td>{{ $p->periodLabel() }}</td>
                            <td>{{ $p->total_hours }}</td>
                            <td>R$ {{ number_format($p->amount, 2, ',', '.') }}</td>
                            <td>
                                @can('markAsPaid', $p)
                                    <form method="POST"
                                          action="{{ route('admin.payments.pay', $p) }}">
                                        @csrf
                                        <button class="btn btn-success btn-sm">
                                            Marcar como pago
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection
