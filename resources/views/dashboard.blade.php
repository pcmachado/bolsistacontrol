@extends('layouts.app')

@section('title', 'Meu Painel')

@push('styles')
<style>
    .quick-add-btn {
        position: fixed;
        bottom: 25px;
        right: 25px;
        z-index: 999;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #0d6efd;
        color: white;
        font-size: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0px 4px 20px rgba(0,0,0,0.25);
        transition: transform 0.2s ease;
    }

    .quick-add-btn:hover {
        transform: scale(1.1);
        color: white;
    }

    .card {
        border-radius: 14px !important;
    }

    .status-badge {
        font-size: 0.8rem;
    }
</style>
@endpush

@section('content')

<div class="container py-4">

    <h2 class="fw-bold mb-4">
        👋 Olá, {{ $user->name }}
        <small class="text-muted fs-6 d-block">
            Frequência {{ str_pad($currentMonth, 2, '0', STR_PAD_LEFT) }}/{{ $currentYear }}
        </small>
    </h2>

    {{-- PROJETO --}}
    @if($project)
        <div class="alert alert-info">
            <strong>Projeto vinculado:</strong> {{ $project->name }} <br>
            <small>{{ $project->description }}</small>
        </div>
    @else
        <div class="alert alert-warning">
            Você ainda não está vinculado a nenhum projeto.
        </div>
    @endif

    {{-- BOTÃO NOVO REGISTRO --}}
    @if($canCreateRecord)
        <a href="{{ route('attendance.create') }}" class="btn btn-primary mb-3">
            <i class="bi bi-plus-circle"></i> Novo Registro de Frequência
        </a>
    @else
        <div class="alert alert-warning mb-3">
            Este mês já foi enviado para homologação.
        </div>
    @endif

    {{-- ========================
         CARDS RESUMO
    ========================= --}}
    <div class="row mb-4">
        @include('attendance.submissions.cards.scholarship_holder.submitted', ['count' => $submissionCounts['enviado'] ?? 0])
        @include('attendance.submissions.cards.scholarship_holder.approved',  ['count' => $submissionCounts['aprovado'] ?? 0])
        @include('attendance.submissions.cards.scholarship_holder.rejected',  ['count' => $submissionCounts['rejeitado'] ?? 0])
        @include('attendance.submissions.cards.scholarship_holder.late',      ['count' => $submissionCounts['atrasado'] ?? 0])
    </div>


    {{-- ========================
         GRÁFICO
    ========================= --}}
    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <strong>Resumo do Mês</strong>
        </div>
        <div class="card-body" style="height: 280px;">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    {{-- ========================
         ÚLTIMOS REGISTROS
    ========================= --}}
    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <strong>Últimas Submissões</strong>
        </div>

        <div class="card-body">
            @if($lastSubmissions->isEmpty())
                <p class="text-muted">Nenhum registro enviado recentemente.</p>
            @else
                <ul class="list-group">
                    @foreach($lastSubmissions as $sub)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>
                                    {{ str_pad($sub->month, 2, '0', STR_PAD_LEFT) }}/{{ $sub->year }}
                                </strong>
                                <br>
                                <small class="text-muted">
                                    {{ ucfirst($sub->status) }}
                                </small>
                            </div>

                            <a href="{{ route('my-attendance.submissions.show', $sub) }}"
                            class="btn btn-sm btn-outline-primary">
                                Abrir
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- ========================
         NOTIFICAÇÕES
    ========================= --}}
    <div class="card shadow-sm mt-4 mb-5">
        <div class="card-header">
            <strong>Notificações Recentes</strong>
        </div>

        <div class="card-body">
            @if($recentNotifications->isEmpty())
                <p class="text-muted">Nenhuma notificação.</p>
            @else
                <ul class="list-group">
                    @foreach($recentNotifications as $not)
                        <li class="list-group-item">
                            {{ $not->data['message'] ?? 'Notificação' }}
                            <br>
                            <small class="text-muted">{{ $not->created_at->diffForHumans() }}</small>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById('attendanceChart').getContext('2d');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Aprovadas', 'Enviadas', 'Rejeitadas', 'Atrasadas'],
            datasets: [{
                data: [
                    {{ $submissionCounts['aprovado'] ?? 0 }},
                    {{ $submissionCounts['enviado'] ?? 0 }},
                    {{ $submissionCounts['rejeitado'] ?? 0 }},
                    {{ $submissionCounts['atrasado'] ?? 0 }}
                ],
                backgroundColor: [
                    '#198754',
                    '#0dcaf0',
                    '#dc3545',
                    '#ffc107'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });

});
</script>
@endpush
