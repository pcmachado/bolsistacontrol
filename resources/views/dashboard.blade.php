@extends('layouts.app')

@section('title', 'Meu Painel')

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
    .filter-card,
    .section-card {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
    }

    .summary-card .value {
        font-size: 1.9rem;
        font-weight: 700;
        line-height: 1;
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

    .section-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .period-nav {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .list-compact .list-group-item {
        padding-top: 0.85rem;
        padding-bottom: 0.85rem;
    }

    .status-pill {
        font-size: 0.75rem;
        letter-spacing: 0.02em;
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

    .tone-secondary {
        background: rgba(108, 117, 125, 0.12);
        color: #6c757d;
    }

    @media (max-width: 767.98px) {
        .dashboard-hero {
            padding: 1.25rem;
        }

        .section-title {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
@php
    $submissionLabels = [
        'draft' => 'Em edição',
        'submitted' => 'Enviada',
        'approved' => 'Homologada',
        'rejected' => 'Rejeitada',
        'late' => 'Atrasada',
        'open' => 'Aberto',
    ];

    $submissionBadgeClasses = [
        'draft' => 'tone-secondary',
        'submitted' => 'tone-info',
        'approved' => 'tone-success',
        'rejected' => 'tone-danger',
        'late' => 'tone-warning',
        'open' => 'tone-primary',
    ];

    $paymentLabels = [
        'sent_to_payment' => 'Enviado ao financeiro',
        'paid' => 'Pago',
        'confirmed' => 'Confirmado',
    ];

    $paymentBadgeClasses = [
        'sent_to_payment' => 'tone-warning',
        'paid' => 'tone-info',
        'confirmed' => 'tone-success',
    ];

    $periodStatus = $submission?->status ?? 'open';
    $periodLabel = $selectedPeriod->translatedFormat('F/Y');
@endphp

<div class="container py-4">
    <div class="dashboard-shell">

        <section class="dashboard-hero">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                <div>
                    <h2 class="fw-bold mb-2">Olá, {{ $user->name }}</h2>
                    <p class="text-muted mb-0">
                        Acompanhe os registros diários de {{ $periodLabel }},
                        o andamento das submissões de {{ $selectedYear }}
                        e o resumo financeiro do seu painel.
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('attendance.my', ['month' => $monthInput]) }}" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-week me-1"></i> Ver registros
                    </a>

                    <a href="{{ route('payments.my', ['month' => $monthInput]) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-cash-stack me-1"></i> Meus pagamentos
                    </a>

                    @if($submission)
                        <a href="{{ route('my-attendance.submissions.show', $submission) }}" class="btn btn-primary">
                            <i class="bi bi-folder2-open me-1"></i> Abrir submissão
                        </a>
                    @elseif($canCreateRecord)
                        <a href="{{ route('attendance.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Novo registro
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <section class="dashboard-grid">
            <div class="card filter-card">
                <div class="card-body">
                    <div class="section-title mb-3">
                        <div>
                            <h5 class="mb-1">Período dos registros</h5>
                            <small class="text-muted">Navega apenas os dados mensais.</small>
                        </div>

                        <div class="period-nav">
                            <a href="{{ $canNavigatePrevPeriod ? route('dashboard', ['project_id' => $activeProjectId,'month' => $previousPeriod->format('Y-m'), 'year' => $selectedYear]) : '#' }}"
                               class="btn btn-sm btn-outline-secondary {{ $canNavigatePrevPeriod ? '' : 'disabled' }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>

                            <span class="fw-semibold">{{ $periodLabel }}</span>

                            <a href="{{ $canNavigateNextPeriod ? route('dashboard', ['project_id' => $activeProjectId,'month' => $nextPeriod->format('Y-m'), 'year' => $selectedYear]) : '#' }}"
                               class="btn btn-sm btn-outline-secondary {{ $canNavigateNextPeriod ? '' : 'disabled' }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>

                    <form method="GET" class="row g-2 align-items-end">
                        <input type="hidden" name="year" value="{{ $selectedYear }}">
                        <input type="hidden" name="project_id" value="{{ $activeProjectId }}">
                        <div class="col-md-8">
                            <label class="form-label">Competência</label>
                            <input type="month" name="month" value="{{ $monthInput }}" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <button class="btn btn-primary w-100">Aplicar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card filter-card">
                <div class="card-body">
                    <div class="section-title mb-3">
                        <div>
                            <h5 class="mb-1">Ano das submissões</h5>
                            <small class="text-muted">A alteração do ano afeta apenas os envios e o financeiro anual.</small>
                        </div>

                        <div class="period-nav">
                            <a href="{{ $canNavigatePrevYear ? route('dashboard', ['project_id' => $activeProjectId,'month' => $monthInput, 'year' => $selectedYear - 1]) : '#' }}"
                               class="btn btn-sm btn-outline-secondary {{ $canNavigatePrevYear ? '' : 'disabled' }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>

                            <span class="fw-semibold">{{ $selectedYear }}</span>

                            <a href="{{ $canNavigateNextYear ? route('dashboard', ['project_id' => $activeProjectId,'month' => $monthInput, 'year' => $selectedYear + 1]) : '#' }}"
                               class="btn btn-sm btn-outline-secondary {{ $canNavigateNextYear ? '' : 'disabled' }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>

                    <form method="GET" class="row g-2 align-items-end">
                        <input type="hidden" name="month" value="{{ $monthInput }}">
                        <input type="hidden" name="project_id" value="{{ $activeProjectId }}">

                        <div class="col-md-8">
                            <label class="form-label">Ano</label>
                            <input type="number" name="year" min="{{ $oldestYear }}" max="{{ $currentYear }}" value="{{ $selectedYear }}" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <button class="btn btn-outline-primary w-100">Aplicar</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        @if($projects->count() > 1)
            <ul class="nav nav-tabs mb-3" id="projectTabs">

                @foreach($projects as $proj)
                    <li class="nav-item">
                        <a class="nav-link project-tab {{ $proj->id == $activeProjectId ? 'active' : '' }}"
                        href="#"
                        data-project="{{ $proj->id }}">
                            {{ $proj->name }}
                        </a>
                    </li>
                @endforeach

            </ul>
        @else
            <div class="alert alert-warning mb-0">
                Você ainda não está vinculado a nenhum projeto.
            </div>
        @endif

        @if($activeProject)
            <div class="alert alert-info">
                Projeto: <strong>{{ $activeProject->name }}</strong>
            </div>
        @else
            <div class="alert alert-warning">
                Nenhum projeto selecionado ou vínculo inexistente.
            </div>
        @endif

        @if($periodStatus === 'rejected')
            <div class="alert alert-danger mb-0">
                <strong>Submissão rejeitada em {{ $periodLabel }}.</strong>
                @if($submission?->rejected_reason)
                    <br>
                    <span>{{ $submission->rejected_reason }}</span>
                @endif
            </div>
        @elseif($periodStatus === 'submitted')
            <div class="alert alert-warning mb-0">
                <strong>Submissao enviada em {{ $periodLabel }}.</strong>
                O período esta aguardando homologação e novos registros ficam bloqueados.
            </div>
        @elseif($periodStatus === 'approved')
            <div class="alert alert-success mb-0">
                <strong>Submissão homologada em {{ $periodLabel }}.</strong>
                O fechamento mensal foi concluído com sucesso.
            </div>
        @elseif($canCreateRecord)
            <div class="alert alert-primary mb-0">
                <strong>Período aberto.</strong>
                Você ainda pode incluir ou ajustar registros diários deste mês.
            </div>
        @endif

        <section>
            <div class="section-title">
                <div>
                    <h4 class="mb-1">Resumo mensal</h4>
                    <small class="text-muted">Indicadores do período {{ $periodLabel }}.</small>
                </div>

                <span class="badge rounded-pill status-pill {{ $submissionBadgeClasses[$periodStatus] ?? 'tone-secondary' }}">
                    {{ $submissionLabels[$periodStatus] ?? ucfirst($periodStatus) }}
                </span>
            </div>

            <div class="dashboard-grid">
                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Registros no mês</div>
                                <div class="value" id="recordsCount">{{ $recordsCount }}</div>
                                <small class="text-muted">{{ $workedDaysCount }} dias com frequência</small>
                            </div>
                            <span class="icon tone-primary">
                                <i class="bi bi-journal-check"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Horas acumuladas</div>
                                <div class="value" id="recordsHours">{{ number_format($recordsHours, 1, ',', '.') }}h</div>
                                <small class="text-muted">de {{ number_format($monthlyLimit, 1, ',', '.') }}h previstas</small>
                            </div>
                            <span class="icon tone-success">
                                <i class="bi bi-stopwatch"></i>
                            </span>
                        </div>

                        <div class="progress mt-3" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $completionPercent }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Estimativa do período</div>
                                <div class="value" id="estimatedValue">R$ {{ number_format($periodEstimatedValue, 2, ',', '.') }}</div>
                                <small class="text-muted">
                                    @if($periodPayment)
                                        {{ $paymentLabels[$periodPayment->status] ?? ucfirst($periodPayment->status) }}
                                    @else
                                        Baseado nos registros e horas do mês
                                    @endif
                                </small>
                            </div>
                            <span class="icon tone-warning">
                                <i class="bi bi-currency-dollar"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Pagamento do período</div>
                                <div class="value" id="periodPayment">
                                    @if($periodPayment)
                                        <span class="fs-4">R$ {{ number_format($periodPayment->amount, 2, ',', '.') }}</span>
                                    @else
                                        <span class="fs-4">--</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    @if($periodPayment)
                                        {{ str_pad($periodPayment->month, 2, '0', STR_PAD_LEFT) }}/{{ $periodPayment->year }}
                                    @else
                                        Ainda sem pagamento gerado para o período
                                    @endif
                                </small>
                            </div>
                            <span class="icon tone-info">
                                <i class="bi bi-wallet2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="section-title">
                <div>
                    <h4 class="mb-1">Submissões em {{ $selectedYear }}</h4>
                    <small class="text-muted">{{ $submissionCounts['total'] ?? 0 }} pacotes mensais encontrados no ano.</small>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Enviadas</div>
                                <div class="value" id="submittedCount">{{ $submissionCounts['submitted'] ?? 0 }}</div>
                                <small class="text-muted">Aguardando homologação</small>
                            </div>
                            <span class="icon tone-info">
                                <i class="bi bi-send"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Homologadas</div>
                                <div class="value" id="approvedCount">{{ $submissionCounts['approved'] ?? 0 }}</div>
                                <small class="text-muted">Concluídas no ano</small>
                            </div>
                            <span class="icon tone-success">
                                <i class="bi bi-patch-check"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Rejeitadas</div>
                                <div class="value" id="rejectedCount">{{ $submissionCounts['rejected'] ?? 0 }}</div>
                                <small class="text-muted">Precisaram de ajuste</small>
                            </div>
                            <span class="icon tone-danger">
                                <i class="bi bi-exclamation-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Atrasadas</div>
                                <div class="value" id="lateCount">{{ $submissionCounts['late'] ?? 0 }}</div>
                                <small class="text-muted">Sem ocorrências registradas</small>
                            </div>
                            <span class="icon tone-warning">
                                <i class="bi bi-alarm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="row g-4">
            <div class="col-xl-5">
                <div class="card section-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Distribuição das submissões</strong>
                        <div class="text-muted small">Leitura anual de {{ $selectedYear }}</div>
                    </div>
                    <div class="card-body px-4 pb-4" style="height: 320px;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="card section-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Resumo financeiro de {{ $selectedYear }}</strong>
                        <div class="text-muted small">{{ $paymentCounts['total'] ?? 0 }} pagamentos vinculados ao ano.</div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="dashboard-grid mb-3">
                            <div class="card summary-card h-100">
                                <div class="card-body">
                                    <div class="text-muted small mb-2">Enviado ao financeiro</div>
                                    <div class="value" id="sentAmount">R$ {{ number_format($paymentTotals['sent'] ?? 0, 2, ',', '.') }}</div>
                                    <small class="text-muted">{{ $paymentCounts['sent'] ?? 0 }} pagamento(s)</small>
                                </div>
                            </div>

                            <div class="card summary-card h-100">
                                <div class="card-body">
                                    <div class="text-muted small mb-2">Pago</div>
                                    <div class="value" id="paidAmount">R$ {{ number_format($paymentTotals['paid'] ?? 0, 2, ',', '.') }}</div>
                                    <small class="text-muted">{{ $paymentCounts['paid'] ?? 0 }} pagamento(s)</small>
                                </div>
                            </div>

                            <div class="card summary-card h-100">
                                <div class="card-body">
                                    <div class="text-muted small mb-2">Confirmado</div>
                                    <div class="value" id="confirmedAmount">R$ {{ number_format($paymentTotals['confirmed'] ?? 0, 2, ',', '.') }}</div>
                                    <small class="text-muted">{{ $paymentCounts['confirmed'] ?? 0 }} pagamento(s)</small>
                                </div>
                            </div>

                            <div class="card summary-card h-100">
                                <div class="card-body">
                                    <div class="text-muted small mb-2">Aguardando confirmação</div>
                                    <div class="value" id="waitingConfirmationAmount">R$ {{ number_format($paymentTotals['waiting_confirmation'] ?? 0, 2, ',', '.') }}</div>
                                    <small class="text-muted">{{ $paymentCounts['waiting_confirmation'] ?? 0 }} pagamento(s)</small>
                                </div>
                            </div>
                        </div>

                        @if($recentPayments->isEmpty())
                            <p class="text-muted mb-0">Nenhum pagamento localizado para {{ $selectedYear }}.</p>
                        @else
                            <ul class="list-group list-compact">
                                @foreach($recentPayments as $payment)
                                    <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                        <div>
                                            <strong>{{ str_pad($payment->month, 2, '0', STR_PAD_LEFT) }}/{{ $payment->year }}</strong>
                                            <br>
                                            <small class="text-muted">R$ {{ number_format($payment->amount, 2, ',', '.') }}</small>
                                        </div>

                                        <span class="badge rounded-pill status-pill {{ $paymentBadgeClasses[$payment->status] ?? 'tone-secondary' }}">
                                            {{ $paymentLabels[$payment->status] ?? ucfirst($payment->status) }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="row g-4">
            <div class="col-xl-7">
                <div class="card section-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Submissões do ano</strong>
                        <div class="text-muted small">Ao trocar apenas o mês, esta lista permanece no ano selecionado.</div>
                    </div>

                    <div class="card-body px-4 pb-4">
                        @if($lastSubmissions->isEmpty())
                            <p class="text-muted mb-0">Nenhuma submissão localizada em {{ $selectedYear }}.</p>
                        @else
                            <ul class="list-group list-compact">
                                @foreach($lastSubmissions as $sub)
                                    <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                        <div>
                                            <strong>{{ str_pad($sub->month, 2, '0', STR_PAD_LEFT) }}/{{ $sub->year }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $submissionLabels[$sub->status] ?? ucfirst($sub->status) }}
                                            </small>
                                        </div>

                                        <a href="{{ route('my-attendance.submissions.show', $sub) }}" class="btn btn-sm btn-outline-primary">
                                            Abrir
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card section-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Notificações recentes</strong>
                        <div class="text-muted small" id="pendingNotifications">{{ $notificacoesPendentes }} nao lida(s)</div>
                    </div>

                    <div class="card-body px-4 pb-4">
                        @if($recentNotifications->isEmpty())
                            <p class="text-muted mb-0">Nenhuma notificação recente.</p>
                        @else
                            <ul class="list-group list-compact">
                                @foreach($recentNotifications as $not)
                                    <li class="list-group-item">
                                        {{ $not->data['message'] ?? 'Notificacao' }}
                                        <br>
                                        <small class="text-muted">{{ $not->created_at->diffForHumans() }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let chart;

    function renderChart(data) {
        const ctx = document.getElementById('attendanceChart').getContext('2d');

        if (chart) {
            chart.destroy();
        }

        chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Homologadas', 'Enviadas', 'Rejeitadas', 'Atrasadas'],
                datasets: [{
                    data: [
                        data.approved ?? 0,
                        data.submitted ?? 0,
                        data.rejected ?? 0,
                        data.late ?? 0
                    ],
                    backgroundColor: ['#198754','#0dcaf0','#dc3545','#ffc107']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    function loadDashboard(projectId) {

        document.body.classList.add('loading');

        fetch(`{{ route('dashboard.stats') }}?project_id=${projectId}&month={{ $monthInput }}&year={{ $selectedYear }}`)
            .then(res => res.json())
            .then(res => {

                const data = res.data;
                const financial = res.financial;
                const attendance = res.attendance;

                // 🔥 Atualizar cards
                document.getElementById('recordsCount').innerText = data.recordsCount;

                document.getElementById('recordsHours').innerText =
                    Number(data.recordsHours).toLocaleString('pt-BR') + 'h';

                document.getElementById('estimatedValue').innerText =
                    'R$ ' + Number(data.periodEstimatedValue).toLocaleString('pt-BR', {minimumFractionDigits: 2});

                // pagamento
                if (financial?.periodPayment) {
                    document.getElementById('periodPayment').innerText =
                        'R$ ' + Number(financial.periodPayment.amount).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                }

                // gráfico
                renderChart(attendance);

                // atualizar URL (sem reload)
                history.replaceState(null, '', '?project_id=' + projectId);

            })
            .catch(() => alert('Erro ao carregar dados'))
            .finally(() => document.body.classList.remove('loading'));
    }

    // 🔥 tabs
    document.querySelectorAll('.project-tab').forEach(tab => {

        tab.addEventListener('click', function (e) {
            e.preventDefault();

            const projectId = this.dataset.project;

            // UI ativa
            document.querySelectorAll('.project-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            loadDashboard(projectId);
        });

    });

});
</script>
@endpush