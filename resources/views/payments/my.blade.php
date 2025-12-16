@extends('layouts.app')

@section('title', 'Meus Pagamentos')

@section('content')
<div class="container">

    <h3 class="mb-4">
        <i class="bi bi-wallet2 me-2"></i>
        Meus Pagamentos
    </h3>

    @if($p->isConfirmed())
        <a href="{{ route('payments.receipt', $p) }}"
        class="btn btn-outline-secondary btn-sm">
            Baixar recibo
        </a>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Período</th>
                        <th>Projeto</th>
                        <th>Unidade</th>
                        <th>Horas</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td>{{ $p->periodLabel() }}</td>
                        <td>{{ $p->project->name ?? '-' }}</td>
                        <td>{{ $p->unit->name ?? '-' }}</td>
                        <td>{{ $p->total_hours }}</td>
                        <td>
                            R$ {{ number_format($p->amount, 2, ',', '.') }}
                        </td>
                        <td>
                            @switch($p->status)
                                @case('sent_to_payment')
                                    <span class="badge bg-warning text-dark">Enviado</span>
                                    @break
                                @case('paid')
                                    <span class="badge bg-info">Pago</span>
                                    @break
                                @case('confirmed')
                                    <span class="badge bg-success">Confirmado</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $p->status }}</span>
                            @endswitch
                        </td>
                        <td>
                            @can('confirm', $p)
                                <form method="POST"
                                      action="{{ route('payments.confirm', $p) }}">
                                    @csrf
                                    <button class="btn btn-success btn-sm">
                                        Confirmar recebimento
                                    </button>
                                </form>
                            @elseif($p->isConfirmed())
                                <span class="text-muted small">
                                    Confirmado em {{ $p->confirmed_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Nenhum pagamento encontrado.
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection
