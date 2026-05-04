@extends('layouts.app')

@section('title', 'Visão Geral - Admin')

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
    .filter-card,
    .nav-card {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
    }

    .summary-card .value {
        font-size: 1.85rem;
        font-weight: 700;
        line-height: 1.05;
    }

    .summary-card .icon,
    .nav-card .icon {
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

    .list-compact .list-group-item {
        padding-top: 0.85rem;
        padding-bottom: 0.85rem;
    }

    .nav-card {
        text-decoration: none;
        color: inherit;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .nav-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.1);
    }
</style>
@endpush

@section('content')
@php
    $periodDescription = request('start_date') && request('end_date')
        ? \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') . ' até ' . \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y')
        : \Carbon\Carbon::createFromFormat('Y-m', $selectedMonthInput)->translatedFormat('F/Y');
@endphp

<div class="container-fluid py-2">
    <div class="dashboard-shell">

        @if($unitName)
            <div class="alert alert-info mb-0">
                <i class="bi bi-building me-1"></i>
                Você está visualizando os dados da <strong>{{ $unitName }}</strong>.
            </div>
        @endif

        @if($projects->count() > 1)
        <ul class="nav nav-tabs mb-3">
            @foreach($projects as $project)
                <li class="nav-item">
                    <a class="nav-link @if($project->id == $activeProjectId) active @endif"
                    href="{{ route('admin.dashboard', ['project_id' => $project->id]) }}">
                        {{ $project->name }}
                    </a>
                </li>
            @endforeach
        </ul>
        @endif

        @if($activeProject)
            <div class="alert alert-info">
                <i class="bi bi-kanban"></i>
                Projeto: <strong>{{ $activeProject->name }}</strong>
            </div>
        @endif

        <section class="dashboard-hero">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                <div>
                    <h1 class="h3 fw-bold mb-2">Dashboard Administrativo</h1>
                    <p class="text-muted mb-0">
                        Visão consolidada da operação, frequência, pagamentos e acesso rápido aos painéis especializados para o período de {{ $periodDescription }}.
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.dashboard.academic') }}" class="btn btn-outline-primary">
                        <i class="bi bi-mortarboard me-1"></i> Dashboard acadêmico
                    </a>
                    <a href="{{ route('admin.payments.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-cash-coin me-1"></i> Financeiro
                    </a>
                    <a href="{{ route('attendance.submissions.index') }}" class="btn btn-primary">
                        <i class="bi bi-calendar-check me-1"></i> Submissões
                    </a>
                </div>
            </div>
        </section>

        <section class="card filter-card">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">

                    <input type="hidden" name="project_id" value="{{ $activeProjectId }}">

                    <div class="col-md-2">
                        <label class="form-label">Competência</label>
                        <input type="month" name="month" value="{{ $selectedMonthInput }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Data inicial</label>
                        <input type="date" name="start_date" value="{{ $selectedStartDate }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Data final</label>
                        <input type="date" name="end_date" value="{{ $selectedEndDate }}" class="form-control">
                    </div>

                    <div class="col-md-4 d-flex gap-2">
                        <button class="btn btn-primary flex-grow-1">
                            <i class="bi bi-funnel me-1"></i> Aplicar filtros
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Limpar</a>
                    </div>
                </form>
            </div>
        </section>

        <section>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">Acesso rápido</h4>
                    <small class="text-muted">Navegação direta para os painéis especializados.</small>
                </div>
            </div>

            <div class="dashboard-grid">
                <a href="{{ route('attendance.submissions.index') }}" class="nav-card card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="icon tone-info"><i class="bi bi-calendar-check"></i></span>
                            <i class="bi bi-arrow-right text-muted"></i>
                        </div>
                        <h5 class="mb-1">Operacional</h5>
                        <p class="text-muted mb-0 small">Submissões, homologações e fluxo de frequência.</p>
                    </div>
                </a>

                <a href="{{ route('admin.payments.dashboard') }}" class="nav-card card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="icon tone-success"><i class="bi bi-cash-stack"></i></span>
                            <i class="bi bi-arrow-right text-muted"></i>
                        </div>
                        <h5 class="mb-1">Financeiro</h5>
                        <p class="text-muted mb-0 small">Pagamentos, saldos, previsões e fechamento.</p>
                    </div>
                </a>

                <a href="{{ route('admin.dashboard.academic') }}" class="nav-card card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="icon tone-primary"><i class="bi bi-mortarboard"></i></span>
                            <i class="bi bi-arrow-right text-muted"></i>
                        </div>
                        <h5 class="mb-1">Acadêmico</h5>
                        <p class="text-muted mb-0 small">Aulas, carga horária, professores, cursos e turmas.</p>
                    </div>
                </a>
            </div>
        </section>

        <section>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">Indicadores institucionais</h4>
                    <small class="text-muted">Resumo estrutural do ambiente acadêmico e operacional.</small>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Projetos ativos</div>
                                <div class="value">{{ $academic['projects_active'] ?? 0 }}</div>
                                <small class="text-muted">Projetos em andamento</small>
                            </div>
                            <span class="icon tone-primary"><i class="bi bi-kanban"></i></span>
                        </div>
                    </div>
                </div>

                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Cursos</div>
                                <div class="value">{{ $academic['courses_total'] ?? 0 }}</div>
                                <small class="text-muted">Cursos cadastrados</small>
                            </div>
                            <span class="icon tone-success"><i class="bi bi-mortarboard"></i></span>
                        </div>
                    </div>
                </div>

                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Turmas ativas</div>
                                <div class="value">{{ $academic['class_offerings_active'] ?? 0 }}</div>
                                <small class="text-muted">Ofertas em execução</small>
                            </div>
                            <span class="icon tone-info"><i class="bi bi-collection"></i></span>
                        </div>
                    </div>
                </div>

                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Bolsistas</div>
                                <div class="value">{{ $scholarshipHoldersCount ?? 0 }}</div>
                                <small class="text-muted">Visíveis no escopo atual</small>
                            </div>
                            <span class="icon tone-warning"><i class="bi bi-people"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">Frequência e financeiro</h4>
                    <small class="text-muted">Leitura rápida do andamento operacional do período.</small>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Submissões pendentes</div>
                                <div class="value">{{ $counts['submitted'] ?? 0 }}</div>
                                <small class="text-muted">{{ $percentages['submitted'] ?? 0 }}% do total do período</small>
                            </div>
                            <span class="icon tone-info"><i class="bi bi-send"></i></span>
                        </div>
                    </div>
                </div>

                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Homologadas</div>
                                <div class="value">{{ $counts['approved'] ?? 0 }}</div>
                                <small class="text-muted">{{ $percentages['approved'] ?? 0 }}% do total do período</small>
                            </div>
                            <span class="icon tone-success"><i class="bi bi-patch-check"></i></span>
                        </div>
                    </div>
                </div>

                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Rejeitadas</div>
                                <div class="value">{{ $counts['rejected'] ?? 0 }}</div>
                                <small class="text-muted">{{ $percentages['rejected'] ?? 0 }}% do total do período</small>
                            </div>
                            <span class="icon tone-danger"><i class="bi bi-exclamation-circle"></i></span>
                        </div>
                    </div>
                </div>

                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Pagamentos gerados</div>
                                <div class="value">{{ $financialOverview['counts']['generated'] ?? 0 }}</div>
                                <small class="text-muted">R$ {{ number_format($financialOverview['totals']['paid'] ?? 0, 2, ',', '.') }} pagos</small>
                            </div>
                            <span class="icon tone-dark"><i class="bi bi-cash-coin"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="row g-4">
            <div class="col-xl-6">
                <div class="card section-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Últimos envios de frequência</strong>
                        <div class="text-muted small">Submissões recentes no escopo atual.</div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if($lastSubmissions->isEmpty())
                            <p class="text-muted mb-0">Nenhum envio recente localizado.</p>
                        @else
                            <ul class="list-group list-compact">
                                @foreach($lastSubmissions as $submission)
                                    <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                        <div>
                                            <strong>{{ $submission->scholarshipHolder?->user?->name ?? 'Bolsista' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ str_pad($submission->month, 2, '0', STR_PAD_LEFT) }}/{{ $submission->year }}</small>
                                        </div>
                                        <span class="badge rounded-pill text-bg-info">Enviado</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card section-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Últimas homologações</strong>
                        <div class="text-muted small">Aprovações recentes no período filtrado.</div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if($lastApprovals->isEmpty())
                            <p class="text-muted mb-0">Nenhuma homologação recente localizada.</p>
                        @else
                            <ul class="list-group list-compact">
                                @foreach($lastApprovals as $approval)
                                    <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                        <div>
                                            <strong>{{ $approval->scholarshipHolder?->user?->name ?? 'Bolsista' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ str_pad($approval->month, 2, '0', STR_PAD_LEFT) }}/{{ $approval->year }}</small>
                                        </div>
                                        <span class="badge rounded-pill text-bg-success">Homologado</span>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const monthInput = document.querySelector('input[name="month"]');
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');

    monthInput?.addEventListener('change', function () {
        if (this.value) {
            if (startDateInput) startDateInput.value = '';
            if (endDateInput) endDateInput.value = '';
        }
    });

    [startDateInput, endDateInput].forEach(function (input) {
        input?.addEventListener('change', function () {
            if ((startDateInput?.value || endDateInput?.value) && monthInput) {
                monthInput.value = '';
            }
        });
    });
});
</script>
@endpush
