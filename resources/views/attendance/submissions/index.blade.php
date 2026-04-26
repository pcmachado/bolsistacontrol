@extends('layouts.app')

@section('title', 'Submissões de Frequência')

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
</style>
@endpush

@section('content')
@php
    $activeMonth = request('month');
    $activeStatus = request('status');
    $activeUnit = request('unit_id');
@endphp

<div class="container-fluid py-2">
    <div class="dashboard-shell">
        <section class="dashboard-hero">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                <div>
                    <h1 class="h3 fw-bold mb-2">Submissões Mensais de Frequência</h1>
                    <p class="text-muted mb-0">
                        Acompanhe o fluxo mensal de envio, homologação e ajustes das frequências com filtros rápidos e uma visão consolidada dos pacotes.
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('attendance.submissions.index', ['month' => $activeMonth ?: now()->format('Y-m')]) }}" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-week me-1"></i> Ver registros
                    </a>
                </div>
            </div>
        </section>

        <section class="dashboard-grid">
            <a href="{{ route('attendance.submissions.index', array_filter(['month' => $activeMonth, 'status' => 'submitted', 'unit_id' => $activeUnit])) }}"
               class="text-decoration-none text-reset">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Pendentes</div>
                                <div class="value">{{ $submissionCounts['submitted'] ?? 0 }}</div>
                                <small class="text-muted">Aguardando homologação</small>
                            </div>
                            <span class="icon tone-info">
                                <i class="bi bi-hourglass-split"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{ route('attendance.submissions.index', array_filter(['month' => $activeMonth, 'status' => 'approved', 'unit_id' => $activeUnit])) }}"
               class="text-decoration-none text-reset">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Homologadas</div>
                                <div class="value">{{ $submissionCounts['approved'] ?? 0 }}</div>
                                <small class="text-muted">Submissões concluídas</small>
                            </div>
                            <span class="icon tone-success">
                                <i class="bi bi-patch-check"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{ route('attendance.submissions.index', array_filter(['month' => $activeMonth, 'status' => 'rejected', 'unit_id' => $activeUnit])) }}"
               class="text-decoration-none text-reset">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Rejeitadas</div>
                                <div class="value">{{ $submissionCounts['rejected'] ?? 0 }}</div>
                                <small class="text-muted">Pacotes com ajustes</small>
                            </div>
                            <span class="icon tone-danger">
                                <i class="bi bi-exclamation-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>

            <a href="{{ route('attendance.submissions.index', array_filter(['month' => $activeMonth, 'status' => 'late', 'unit_id' => $activeUnit])) }}"
               class="text-decoration-none text-reset">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-2">Atrasadas</div>
                                <div class="value">{{ $submissionCounts['late'] ?? 0 }}</div>
                                <small class="text-muted">Ocorrências fora do prazo</small>
                            </div>
                            <span class="icon tone-warning">
                                <i class="bi bi-alarm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </section>

        @include('attendance.submissions.partials.filters')

        <section class="card section-card">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <strong>Lista de submissões</strong>
                <div class="text-muted small">
                    @if($activeMonth)
                        Competência filtrada: {{ \Carbon\Carbon::createFromFormat('Y-m', $activeMonth)->translatedFormat('F/Y') }}
                    @else
                        Exibindo todos os períodos visíveis para o perfil atual.
                    @endif
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                {!! $dataTable->table() !!}
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
