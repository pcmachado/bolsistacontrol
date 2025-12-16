@extends('layouts.app')

@section('title', 'Configurações de Alertas Inteligentes')

@section('content')
<div class="container">

    <h2 class="fw-bold mb-4">
        <i class="bi bi-gear-fill me-2"></i>
        Configurações de Alertas Inteligentes
    </h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.settings.alerts.update') }}" method="POST">
        @csrf

        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <h5 class="fw-semibold mb-3">Disciplina Atrasada</h5>

                <div class="mb-3">
                    <label class="form-label">Percentual mínimo de progresso</label>
                    <input type="number" step="0.01" name="delay_percent_threshold"
                           value="{{ $settings->delay_percent_threshold }}"
                           class="form-control">
                    <small class="text-muted">Ex: 0.8 = 80% do tempo transcorrido</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Papéis que recebem notificação</label>
                    <input type="text" name="delay_notify_roles"
                           value="{{ $settings->delay_notify_roles }}"
                           class="form-control">
                    <small class="text-muted">Ex: coordenador_adjunto,supervisor</small>
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" name="check_delays_enabled" value="1"
                           class="form-check-input"
                           @checked($settings->check_delays_enabled)>
                    <label class="form-check-label">Ativar alerta de disciplina atrasada</label>
                </div>

                <hr>

                <h5 class="fw-semibold mb-3">Turma sem Aulas Recentes</h5>

                <div class="mb-3">
                    <label class="form-label">Dias sem aula para alertar</label>
                    <input type="number" name="no_class_days"
                           value="{{ $settings->no_class_days }}"
                           class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Papéis que recebem notificação</label>
                    <input type="text" name="no_class_notify_roles"
                           value="{{ $settings->no_class_notify_roles }}"
                           class="form-control">
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" name="check_no_class_enabled" value="1"
                           class="form-check-input"
                           @checked($settings->check_no_class_enabled)>
                    <label class="form-check-label">Ativar alerta de turma sem aulas</label>
                </div>

                <button class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-2"></i> Salvar Configurações
                </button>

            </div>
        </div>

    </form>
</div>
@endsection
