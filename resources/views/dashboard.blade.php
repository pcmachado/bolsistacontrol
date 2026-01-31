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

    <h2 class="fw-bold mb-4">👋 Olá, {{ $user->name }}</h2>

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
    <div class="mb-3">
        <a href="{{ route('attendance.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Registro de Frequência
        </a>
    </div>

    {{-- ========================
         CARDS RESUMO
    ========================= --}}
    <div class="row mb-4">
        @include('attendance.submissions.cards.subbmited')
        @include('attendance.submissions.cards.approved')
        @include('attendance.submissions.cards.rejected')
        @include('attendance.submissions.cards.late')
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
                                <strong>{{ $sub->scholarshipHolder->user->name }}</strong><br>
                                <small>{{ \Carbon\Carbon::parse($sub->date)->format('d/m/Y') }}</small>
                            </div>
                            <span class="badge bg-info status-badge">Enviado</span>
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
document.addEventListener("DOMContentLoaded", function() {

    const ctx = document.getElementById('attendanceChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Aprov.', 'Subm.', 'Rej.', 'Rasc.', 'Atr.'],
            datasets: [{
                label: 'Registros',
                data: [
                    {{ $counts['approved'] }},
                    {{ $counts['submitted'] }},
                    {{ $counts['rejected'] }},
                    {{ $counts['draft'] }},
                    {{ $counts['late'] }}
                ],
                backgroundColor: [
                    'rgba(25, 135, 84, .7)',
                    'rgba(13, 202, 240, .7)',
                    'rgba(220, 53, 69, .7)',
                    'rgba(108, 117, 125, .7)',
                    'rgba(255, 193, 7, .7)'
                ],
                borderWidth: 0
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
