@extends('layouts.app')

@section('title', 'Pagamentos')

@section('content')
<div class="container-fluid">

    <h1 class="mb-4">Pagamentos</h1>

    {{-- Filtro --}}
    <form class="mb-3 d-flex gap-2">
        <select name="status" class="form-select w-auto" onchange="this.form.submit()">
            @foreach([
                'sent_to_payment' => 'Enviados',
                'paid' => 'Pagos',
                'confirmed' => 'Confirmados'
            ] as $key => $label)
                <option value="{{ $key }}" @selected($status === $key)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Bolsista</th>
                    <th>Unidade</th>
                    <th>Projeto</th>
                    <th>Período</th>
                    <th>Horas</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->scholarshipHolder->user->name }}</td>
                        <td>{{ $payment->unit->name }}</td>
                        <td>{{ $payment->project->name ?? '-' }}</td>
                        <td>{{ $payment->periodLabel() }}</td>
                        <td>{{ $payment->total_hours }}</td>
                        <td>R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ strtoupper($payment->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($payment->isSent())
                                <form method="POST"
                                      action="{{ route('admin.payments.pay', $payment) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success">
                                        Marcar como pago
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-muted text-center">
                            Nenhum pagamento encontrado
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
