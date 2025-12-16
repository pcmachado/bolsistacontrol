@extends('layouts.app')

@section('title', 'Dashboard da Disciplina')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">
            <i class="bi bi-journal-bookmark-fill me-2"></i>
            Disciplina: {{ $discipline->name }} — Turma {{ $offering->name }}
        </h2>

        <a href="{{ route('admin.class-offerings.sessions.index', $offering->id) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    {{-- KPIs --}}
    <div class="row mb-4 text-center">

        <x-kpi-card title="Carga Horária Prevista" value="{{ $plannedHours }}h" class="col-md-3" />

        <x-kpi-card title="Ministrado" value="{{ number_format($taughtHours, 1) }}h" class="col-md-3" />

        <x-kpi-card title="Faltam" value="{{ $remainingHours }}h" class="col-md-3" />

        <x-kpi-card title="Progresso" value="{{ $percent }}%" class="col-md-3" />

    </div>

    {{-- Gráfico: Horas por Mês --}}
    <x-chart-card id="hoursByMonthChart" title="Horas Ministradas por Mês" />

    {{-- Gráfico: Horas por Dia da Semana --}}
    <x-chart-card id="hoursByWeekdayChart" title="Distribuição por Dia da Semana" />

    {{-- Timeline --}}
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-clock-history me-2"></i> Linha do Tempo de Aulas
        </div>

        <div class="card-body">
            <canvas id="timelineChart"></canvas>
        </div>
    </div>

    {{-- Lista de Aulas --}}
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-list-task me-2"></i> Aulas Registradas
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Professor</th>
                        <th>Início</th>
                        <th>Fim</th>
                        <th>Horas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $s)
                        <tr>
                            <td>{{ $s->date->format('d/m/Y') }}</td>
                            <td>{{ $s->teacher->name }}</td>
                            <td>{{ $s->start_time }}</td>
                            <td>{{ $s->end_time }}</td>
                            <td>{{ number_format($s->duration_hours, 1) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('hoursByMonthChart'), {
    type: 'bar',
    data: {
        labels: @json($hoursByMonth->pluck('label')),
        datasets: [{
            label: 'Horas',
            data: @json($hoursByMonth->pluck('hours')),
            backgroundColor: '#0d6efd'
        }]
    }
});

new Chart(document.getElementById('hoursByWeekdayChart'), {
    type: 'bar',
    data: {
        labels: @json($hoursByWeekday->pluck('label')),
        datasets: [{
            label: 'Horas',
            data: @json($hoursByWeekday->pluck('hours')),
            backgroundColor: '#198754'
        }]
    }
});

new Chart(document.getElementById('timelineChart'), {
    type: 'line',
    data: {
        labels: @json($timeline->pluck('date')),
        datasets: [{
            label: 'Horas',
            data: @json($timeline->pluck('hours')),
            borderColor: '#dc3545',
            fill: false
        }]
    }
});
</script>

@endpush
