@extends('layouts.app')

@section('title', 'Relatório Financeiro')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">
        <i class="bi bi-cash-stack"></i> Relatório Financeiro
    </h3>

    {{-- ============================= --}}
    {{-- FILTROS --}}
    {{-- ============================= --}}
    <form method="GET" class="card p-3 shadow-sm mb-4">
        <div class="row g-3">

            {{-- COMPETÊNCIA (MÊS/ANO UNIFICADO) --}}
            <div class="col-md-3">
                <label>Competência</label>
                <input type="month" name="month" class="form-control"
                       value="{{ request('month', now()->format('Y-m')) }}">
            </div>

            {{-- PROJETO --}}
            <div class="col-md-3">
                <label>Projeto</label>
                <select name="project_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}"
                            {{ request('project_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- UNIDADE --}}
            <div class="col-md-3">
                <label>Unidade</label>
                <select name="unit_id" class="form-select">
                    <option value="">Todas</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}"
                            {{ request('unit_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- STATUS --}}
            <div class="col-md-3">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="sent_to_payment" {{ request('status') == 'sent_to_payment' ? 'selected' : '' }}>Enviado</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Pago</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                </select>
            </div>

            {{-- PERÍODO POR DATA (OPCIONAL) --}}
            <div class="col-md-3">
                <label>Data início</label>
                <input type="date" name="start_date" class="form-control"
                       value="{{ request('start_date') }}">
            </div>

            <div class="col-md-3">
                <label>Data fim</label>
                <input type="date" name="end_date" class="form-control"
                       value="{{ request('end_date') }}">
            </div>

            {{-- BOTÕES --}}
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">
                    🔍 Filtrar
                </button>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('admin.financial-reports.pdf', request()->query()) }}"
                   class="btn btn-danger w-100" target="_blank">
                    📄 PDF
                </a>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('admin.financial-reports.excel', request()->query()) }}"
                   class="btn btn-success w-100">
                    📊 Excel
                </a>
            </div>

        </div>
    </form>

    {{-- ============================= --}}
    {{-- RESUMO --}}
    {{-- ============================= --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">

            <h5 class="mb-0">
                Total do período:
                <strong class="text-success">
                    R$ {{ number_format($total, 2, ',', '.') }}
                </strong>
            </h5>

        </div>
    </div>

    {{-- ============================= --}}
    {{-- RESULTADOS --}}
    {{-- ============================= --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Período</th>
                        <th>Bolsista</th>
                        <th>Projeto</th>
                        <th>Unidade</th>
                        <th>Status</th>
                        <th class="text-end">Valor</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td>{{ str_pad($p->month, 2, '0', STR_PAD_LEFT) }}/{{ $p->year }}</td>
                        <td>{{ $p->scholarshipHolder?->user?->name ?? '-' }}</td>
                        <td>{{ $p->project?->name ?? '-' }}</td>
                        <td>{{ $p->unit?->name ?? '-' }}</td>
                        <td>
                            @include('admin.financial_reports.status_badge', ['p' => $p])
                        </td>
                        <td class="text-end">
                            R$ {{ number_format($p->amount, 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Nenhum pagamento encontrado para os filtros informados.
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection