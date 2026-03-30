@extends('layouts.app')

@section('title', 'Dashboard Financeiro')

@section('content')
<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-cash-coin me-2"></i> Dashboard Financeiro
        </h3>
        <span class="text-muted">
            {{ str_pad($month,2,'0',STR_PAD_LEFT) }}/{{ $year }}
        </span>
    </div>

    {{-- ================= ALERTAS ================= --}}
    @if(!empty($alerts))
        <div class="mb-4">
            @foreach($alerts as $alert)
                <div class="alert alert-{{ $alert['type'] }} d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $alert['title'] }}</strong><br>
                        {{ $alert['message'] }}
                    </div>
                    <a href="{{ $alert['action_url'] }}"
                       class="btn btn-sm btn-outline-dark">
                        Ver
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ================= FILTROS ================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payments.dashboard') }}" class="row g-3 align-items-end">

                <div class="col-md-2">
                    <label class="form-label">Mês</label>
                    <input type="number" class="form-control"
                           name="month" value="{{ $month }}" min="1" max="12">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Ano</label>
                    <input type="number" class="form-control"
                           name="year" value="{{ $year }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Projeto</label>
                    <select name="project_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected(request('project_id') == $p->id)>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Unidade</label>
                    <select name="unit_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" @selected(request('unit_id') == $u->id)>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ================= FECHAMENTO ================= --}}
    <div class="mb-4">
        @if($isClosed)
            <div class="alert alert-success">
                <i class="bi bi-lock-fill me-1"></i>
                Período já encerrado.
            </div>
        @else
            <form method="POST" action="{{ route('admin.financial-closures.store') }}">
                @csrf
                <input type="hidden" name="unit_id" value="{{ request('unit_id') }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">

                <button class="btn btn-danger"
                        onclick="return confirm('Confirmar fechamento financeiro do período?')">
                    <i class="bi bi-lock-fill me-1"></i> Fechar período
                </button>
            </form>
        @endif
    </div>

    {{-- ================= CARDS ================= --}}
    @php
        $baseTotal = max($totalPaid + $totalPending + $totalConfirmed, 1);
    @endphp

    <div class="row g-3 mb-4">

        {{-- TOTAL PAGO --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Pago</span>
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                    <h4 class="fw-bold mt-2">
                        R$ {{ number_format($totalPaid, 2, ',', '.') }}
                    </h4>
                    <div class="small text-muted">
                        {{ number_format(($totalPaid / $baseTotal) * 100, 1) }}%
                    </div>
                </div>
            </div>
        </div>

        {{-- CONFIRMADO --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Confirmado</span>
                        <i class="bi bi-patch-check text-primary fs-4"></i>
                    </div>
                    <h4 class="fw-bold mt-2">
                        R$ {{ number_format($totalConfirmed, 2, ',', '.') }}
                    </h4>
                    <div class="small text-muted">
                        {{ number_format(($totalConfirmed / $baseTotal) * 100, 1) }}%
                    </div>
                </div>
            </div>
        </div>

        {{-- PENDENTE --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Pendente</span>
                        <i class="bi bi-hourglass-split text-warning fs-4"></i>
                    </div>
                    <h4 class="fw-bold mt-2">
                        R$ {{ number_format($totalPending, 2, ',', '.') }}
                    </h4>
                    <div class="small text-muted">
                        {{ number_format(($totalPending / $baseTotal) * 100, 1) }}%
                    </div>
                </div>
            </div>
        </div>

        {{-- QUANTIDADE PENDENTE --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-4 border-danger">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Pendentes</span>
                        <i class="bi bi-hourglass-split text-danger fs-4"></i>
                    </div>

                    {{-- DESTAQUE --}}
                    <h4 class="fw-bold mt-2">
                        {{ $countPending }}
                    </h4>

                    {{-- DETALHES --}}
                    <div class="small text-muted">
                        Recebidos: <strong>{{ $totalCount }}</strong>
                        Pagos: <strong>{{ $countPaid }}</strong>
                        Confirmados: <strong>{{ $countConfirmed }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= COMPARAÇÃO ================= --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Comparação mensal</span>

                        @if(!is_null($variation))
                            <span class="{{ $variation >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                                <i class="bi {{ $variation >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                                {{ number_format(abs($variation), 2, ',', '.') }}%
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>

                    <h6 class="mt-3 mb-1">
                        {{ str_pad($prevMonth,2,'0',STR_PAD_LEFT) }}/{{ $prevYear }}
                        →
                        {{ str_pad($month,2,'0',STR_PAD_LEFT) }}/{{ $year }}
                    </h6>

                    <div class="small text-muted">
                        R$ {{ number_format($previousTotal, 2, ',', '.') }}
                        →
                        <strong>R$ {{ number_format($currentTotal, 2, ',', '.') }}</strong>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ================= GRÁFICOS ================= --}}
    <div class="row g-4 mb-4">

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    Pagamentos por Projeto
                </div>
                <div class="card-body">
                    <canvas id="chartProject"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    Pagamentos por Unidade
                </div>
                <div class="card-body">
                    <canvas id="chartUnit"></canvas>
                </div>
            </div>
        </div>

    </div>
    {{-- ================= EVOLUÇÃO ANUAL ================= --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    Evolução Financeira (Status)
                </div>
                <div class="card-body">
                    <canvas id="chartStacked"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

new Chart(document.getElementById('chartProject'), {
    type: 'pie',
    data: {
        labels: {!! json_encode($chartByProject->pluck('project.name')) !!},
        datasets: [{
            data: {!! json_encode($chartByProject->pluck('total')) !!},
            backgroundColor: [
                '#198754','#0d6efd','#ffc107','#dc3545',
                '#6f42c1','#20c997','#fd7e14'
            ]
        }]
    },
    options: {
        plugins: { legend: { position: 'bottom' } }
    }
});

new Chart(document.getElementById('chartUnit'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartByUnit->pluck('unit.name')) !!},
        datasets: [{
            data: {!! json_encode($chartByUnit->pluck('total')) !!},
            backgroundColor: '#0d6efd'
        }]
    },
    options: {
        plugins: { legend: { display: false } }
    }
});

new Chart(document.getElementById('chartStacked'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartStacked['months']) !!},
        datasets: [
            {
                label: 'Pagos',
                data: {!! json_encode($chartStacked['paid']) !!},
                backgroundColor: '#198754'
            },
            {
                label: 'Confirmados',
                data: {!! json_encode($chartStacked['confirmed']) !!},
                backgroundColor: '#0d6efd'
            },
            {
                label: 'Pendentes',
                data: {!! json_encode($chartStacked['pending']) !!},
                backgroundColor: '#ffc107'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': R$ ' +
                            context.raw.toLocaleString('pt-BR', {
                                minimumFractionDigits: 2
                            });
                    }
                }
            }
        },
        scales: {
            x: { stacked: true },
            y: { stacked: true, beginAtZero: true }
        }
    }
});

</script>
@endpush
