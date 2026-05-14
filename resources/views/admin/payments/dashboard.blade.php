@extends('layouts.app')

@section('title', 'Dashboard Financeiro')

@push('styles')
<style>
    .dashboard-shell {
        display: grid;
        gap: 1.5rem;
    }

    .dashboard-hero {
        background: linear-gradient(135deg, #ffffff 0%, #eef5ff 100%);
        border: 1px solid rgba(13, 110, 253, 0.12);
        border-radius: 18px;
        padding: 1.5rem;
        box-shadow: 0 12px 30px rgba(13, 110, 253, 0.08);
    }

    .dashboard-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .summary-card,
    .section-card,
    .filter-card {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
    }

    .summary-card .value {
        font-size: 1.85rem;
        font-weight: 700;
        line-height: 1.05;
    }

    .summary-card .icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .tone-primary {
        background: rgba(13, 110, 253, 0.12);
        color: #0d6efd;
    }

    .tone-success {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
    }

    .tone-danger {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
    }

    .tone-warning {
        background: rgba(255, 193, 7, 0.18);
        color: #997404;
    }

    .tone-info {
        background: rgba(13, 202, 240, 0.14);
        color: #087990;
    }

    .tone-dark {
        background: rgba(33, 37, 41, 0.1);
        color: #212529;
    }
</style>
@endpush

@section('content')
@php
    $monthInput = sprintf('%04d-%02d', $year, $month);
    $periodLabel = \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F/Y');
    $baseTotal = max($totalPaid + $totalPending + $totalConfirmed, 1);
    $unitFilterSelected = filled(request('unit_id'));
@endphp

<div class="container-fluid py-2">
    <div class="dashboard-shell">
        <section class="dashboard-hero">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                <div>
                    <h1 class="h3 fw-bold mb-2">Dashboard Financeiro</h1>
                    <p class="text-muted mb-0">
                        Monitore pagamentos enviados, pagos e confirmados em {{ $periodLabel }}, com cortes por projeto e unidade.
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.payments.index', ['month' => $month, 'year' => $year, 'project_id' => request('project_id'), 'unit_id' => request('unit_id')]) }}" class="btn btn-outline-primary">
                        <i class="bi bi-list-ul me-1"></i> Ver pagamentos
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard admin
                    </a>
                </div>
            </div>
        </section>

        @if(!empty($alerts))
            <section class="dashboard-shell">
                @foreach($alerts as $alert)
                    <div class="alert alert-{{ $alert['type'] }} d-flex justify-content-between align-items-center gap-3 mb-0">
                        <div>
                            <strong>{{ $alert['title'] }}</strong><br>
                            <span>{{ $alert['message'] }}</span>
                        </div>
                        <a href="{{ $alert['action_url'] }}" class="btn btn-sm btn-outline-dark">Ver</a>
                    </div>
                @endforeach
            </section>
        @endif

        <section class="card filter-card">
            <div class="card-body">
                <x-month-navigation
                    route="admin.payments.dashboard"
                    :month="$monthInput"
                    :params="request()->except('month')"
                />

                <form method="GET" action="{{ route('admin.payments.dashboard') }}" class="row g-3 align-items-end">
                    <input type="hidden" name="month" value="{{ $monthInput }}">

                    <div class="col-md-4">
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

                    <div class="col-md-4">
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

                    <div class="col-md-4 d-flex gap-2">
                        <button class="btn btn-primary flex-grow-1">
                            <i class="bi bi-funnel me-1"></i> Aplicar filtros
                        </button>

                        <a href="{{ route('admin.payments.dashboard') }}" class="btn btn-outline-secondary">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>
        </section>

        <section class="d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-between">
            <div>
                @if($isClosed)
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-lock-fill me-1"></i> Período encerrado para {{ $periodLabel }}.
                    </div>
                @elseif(!$unitFilterSelected)
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-info-circle me-1"></i> Selecione uma unidade para permitir o fechamento financeiro do período.
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-unlock me-1"></i> Período ainda aberto para fechamento financeiro.
                    </div>
                @endif
            </div>

            @if(!$isClosed)
                <form method="POST" action="{{ route('admin.financial-closures.store') }}">
                    @csrf
                    <input type="hidden" name="unit_id" value="{{ request('unit_id') }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">

                    <button class="btn btn-danger"
                            {{ $unitFilterSelected ? '' : 'disabled' }}
                            onclick="return confirm('Confirmar fechamento financeiro do período?')">
                        <i class="bi bi-lock-fill me-1"></i> Fechar período
                    </button>
                </form>
            @endif
        </section>

        <section class="dashboard-grid">
            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Total pago</div>
                            <div class="value">R$ {{ number_format($currentTotal, 2, ',', '.') }}</div>
                            <small class="text-muted">{{ number_format(($currentTotal / $baseTotal) * 100, 1, ',', '.') }}% do total liquidado</small>
                        </div>
                        <span class="icon tone-success"><i class="bi bi-check-circle"></i></span>
                    </div>
                </div>
            </div>

            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Confirmado</div>
                            <div class="value">R$ {{ number_format($totalConfirmed, 2, ',', '.') }}</div>
                            <small class="text-muted">{{ $countConfirmed }} pagamento(s)</small>
                        </div>
                        <span class="icon tone-primary"><i class="bi bi-patch-check"></i></span>
                    </div>
                </div>
            </div>

            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Aguardando confirmação</div>
                            <div class="value">R$ {{ number_format($totalPaid, 2, ',', '.') }}</div>
                            <small class="text-muted">{{ $countPaid }} pagamento(s)</small>
                        </div>
                        <span class="icon tone-primary"><i class="bi bi-patch-check"></i></span>
                    </div>
                </div>
            </div>

            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Pendente</div>
                            <div class="value">R$ {{ number_format($totalPending, 2, ',', '.') }}</div>
                            <small class="text-muted">{{ $countPending }} pagamento(s) aguardando execução</small>
                        </div>
                        <span class="icon tone-warning"><i class="bi bi-hourglass-split"></i></span>
                    </div>
                </div>
            </div>

            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Previsto</div>
                            <div class="value">R$ {{ number_format($forecastTotal, 2, ',', '.') }}</div>
                            <small class="text-muted">{{ $forecastCount }} submissão(ões) homologadas</small>
                        </div>
                        <span class="icon tone-dark"><i class="bi bi-graph-up-arrow"></i></span>
                    </div>
                </div>
            </div>

            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Saldo do período</div>
                            <div class="value">R$ {{ number_format($gap, 2, ',', '.') }}</div>
                            <small class="text-muted">{{ $gap > 0 ? 'Ainda a pagar' : 'Liquidado ou acima do previsto' }}</small>
                        </div>
                        <span class="icon {{ $gap > 0 ? 'tone-warning' : 'tone-success' }}">
                            <i class="bi {{ $gap > 0 ? 'bi-exclamation-triangle' : 'bi-check2-circle' }}"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Comparação mensal</div>
                            <div class="value">
                                {{ is_null($variation) ? '—' : number_format($variation, 1, ',', '.') . '%' }}
                            </div>
                            <small class="text-muted">
                                {{ str_pad($prevMonth, 2, '0', STR_PAD_LEFT) }}/{{ $prevYear }} → {{ str_pad($month, 2, '0', STR_PAD_LEFT) }}/{{ $year }}
                            </small>
                        </div>
                        <span class="icon {{ is_null($variation) || $variation >= 0 ? 'tone-success' : 'tone-danger' }}">
                            <i class="bi {{ is_null($variation) || $variation >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">Orçamento dos projetos</h4>
                    <small class="text-muted">Leitura baseada no valor alocado do projeto; quando não houver valor alocado, usa o total da fonte pagadora.</small>
                </div>
            </div>

            <div class="dashboard-grid mb-3">
                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Consolidado alocado</div>
                                <div class="value">R$ {{ number_format($budgetAllocated, 2, ',', '.') }}</div>
                                <small class="text-muted">Total reservado no escopo filtrado</small>
                            </div>
                            <span class="icon tone-primary"><i class="bi bi-piggy-bank"></i></span>
                        </div>
                    </div>
                </div>

                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Consolidado utilizado</div>
                                <div class="value">R$ {{ number_format($budgetUsed, 2, ',', '.') }}</div>
                                <small class="text-muted">{{ number_format($budgetCommitmentPercent, 1, ',', '.') }}% do orçamento comprometido</small>
                            </div>
                            <span class="icon tone-warning"><i class="bi bi-cash-coin"></i></span>
                        </div>
                    </div>
                </div>

                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Consolidado disponível</div>
                                <div class="value">R$ {{ number_format($budgetAvailable, 2, ',', '.') }}</div>
                                <small class="text-muted">{{ $budgetAvailable > 0 ? 'Disponível para execução' : 'Sem saldo disponível' }}</small>
                            </div>
                            <span class="icon {{ $budgetAvailable > 0 ? 'tone-success' : 'tone-danger' }}">
                                <i class="bi {{ $budgetAvailable > 0 ? 'bi-wallet2' : 'bi-exclamation-triangle' }}"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if($projectBudgetSummaries->isEmpty())
                <div class="alert alert-light border mb-0">
                    Nenhum vínculo orçamentário encontrado para os filtros atuais.
                </div>
            @else
                <div class="card section-card">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Orçamento por projeto</strong>
                        <div class="text-muted small">
                            @if($projectBudgetSummaries->count() > 1)
                                Cada aba mostra os indicadores de um projeto para evitar misturar realidades orçamentárias.
                            @else
                                Indicadores detalhados do projeto filtrado.
                            @endif
                        </div>
                    </div>

                    <div class="card-body px-4 pb-4">
                        @if($projectBudgetSummaries->count() > 1)
                            <ul class="nav nav-tabs mb-4" role="tablist">
                                @foreach($projectBudgetSummaries as $summary)
                                    <li class="nav-item" role="presentation">
                                        <button
                                            class="nav-link {{ (string) $summary['project_id'] === (string) $activeProjectBudgetId ? 'active' : '' }}"
                                            data-bs-toggle="tab"
                                            data-bs-target="#project-budget-{{ $summary['project_id'] }}"
                                            type="button"
                                            role="tab">
                                            {{ $summary['project_name'] }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="tab-content">
                            @foreach($projectBudgetSummaries as $summary)
                                <div
                                    class="tab-pane fade {{ (string) $summary['project_id'] === (string) $activeProjectBudgetId ? 'show active' : '' }}"
                                    id="project-budget-{{ $summary['project_id'] }}"
                                    role="tabpanel">

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h5 class="mb-1">{{ $summary['project_name'] }}</h5>
                                            <small class="text-muted">
                                                {{ number_format($summary['commitment_percent'], 1, ',', '.') }}% do orçamento comprometido.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="dashboard-grid">
                                        <div class="card summary-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="text-muted small mb-2">Alocado</div>
                                                        <div class="value">R$ {{ number_format($summary['allocated_total'], 2, ',', '.') }}</div>
                                                        <small class="text-muted">Orçamento reservado ao projeto</small>
                                                    </div>
                                                    <span class="icon tone-primary"><i class="bi bi-piggy-bank"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card summary-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="text-muted small mb-2">Utilizado</div>
                                                        <div class="value">R$ {{ number_format($summary['used_total'], 2, ',', '.') }}</div>
                                                        <small class="text-muted">Valor já comprometido</small>
                                                    </div>
                                                    <span class="icon tone-warning"><i class="bi bi-cash-coin"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card summary-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="text-muted small mb-2">Disponível</div>
                                                        <div class="value">R$ {{ number_format($summary['available_total'], 2, ',', '.') }}</div>
                                                        <small class="text-muted">
                                                            {{ $summary['available_total'] > 0 ? 'Saldo restante do projeto' : 'Sem saldo restante' }}
                                                        </small>
                                                    </div>
                                                    <span class="icon {{ $summary['available_total'] > 0 ? 'tone-success' : 'tone-danger' }}">
                                                        <i class="bi {{ $summary['available_total'] > 0 ? 'bi-wallet2' : 'bi-exclamation-triangle' }}"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </section>

        <section class="row g-4">
            <div class="col-xl-6">
                <div class="card section-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Pagamentos por projeto</strong>
                        <div class="text-muted small">Distribuição financeira do período filtrado.</div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <canvas id="chartProject"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card section-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Pagamentos por unidade</strong>
                        <div class="text-muted small">Consolidação por unidade responsável.</div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <canvas id="chartUnit"></canvas>
                    </div>
                </div>
            </div>
        </section>

        <section class="card section-card">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <strong>Evolução financeira do ano</strong>
                <div class="text-muted small">Comparativo mensal entre pendentes, pagos e confirmados.</div>
            </div>
            <div class="card-body px-4 pb-4">
                <canvas id="chartStacked"></canvas>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chartProject'), {
    type: 'pie',
    data: {
        labels: {!! json_encode($chartByProject->pluck('project.name')->map(fn ($name) => $name ?: 'Sem projeto')) !!},
        datasets: [{
            data: {!! json_encode($chartByProject->pluck('total')) !!},
            backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545', '#6f42c1', '#20c997', '#fd7e14']
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

new Chart(document.getElementById('chartUnit'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartByUnit->pluck('unit.name')->map(fn ($name) => $name ?: 'Sem unidade')) !!},
        datasets: [{
            data: {!! json_encode($chartByUnit->pluck('total')) !!},
            backgroundColor: '#0d6efd'
        }]
    },
    options: {
        plugins: {
            legend: { display: false }
        }
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
                        return context.dataset.label + ': R$ ' + context.raw.toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
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
