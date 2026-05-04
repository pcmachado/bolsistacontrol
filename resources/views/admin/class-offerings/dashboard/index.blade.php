@extends('layouts.app')

@section('title', 'Dashboard da Turma')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">
            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard – {{ $offering->name }}
        </h2>

        <a href="{{ route('admin.class-offerings.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    {{-- KPIs --}}
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h3 class="fw-bold">{{ number_format($totalHours, 1) }}</h3>
                    <p class="text-muted mb-0">Horas Ministradas</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h3 class="fw-bold">{{ $totalClasses }}</h3>
                    <p class="text-muted mb-0">Aulas Registradas</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h3 class="fw-bold">{{ $teachersCount }}</h3>
                    <p class="text-muted mb-0">Professores</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h3 class="fw-bold">{{ $disciplinesCount }}</h3>
                    <p class="text-muted mb-0">Disciplinas Ativas</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Gráfico 1 — Horas por Disciplina --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold bg-white">
            <i class="bi bi-bar-chart-steps me-2"></i> Horas por Disciplina
        </div>
        <div class="card-body">
            <canvas id="hoursByDisciplineChart"></canvas>
        </div>
    </div>

    {{-- Gráfico 2 — Horas por Professor --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold bg-white">
            <i class="bi bi-person-lines-fill me-2"></i> Horas por Professor
        </div>
        <div class="card-body">
            <canvas id="hoursByTeacherChart"></canvas>
        </div>
    </div>

    {{-- Gráfico 3 — Horas por Mês --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold bg-white">
            <i class="bi bi-calendar-range me-2"></i> Horas por Mês
        </div>
        <div class="card-body">
            <canvas id="hoursByMonthChart"></canvas>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const disciplineData = @json($hoursByDiscipline->pluck('hours'));
const disciplineLabels = @json($hoursByDiscipline->pluck('label'));

new Chart(document.getElementById('hoursByDisciplineChart'), {
    type: 'bar',
    data: {
        labels: disciplineLabels,
        datasets: [{
            label: 'Horas Ministradas',
            data: disciplineData,
            backgroundColor: '#0d6efd'
        }]
    }
});

const teacherData = @json($hoursByTeacher->pluck('hours'));
const teacherLabels = @json($hoursByTeacher->pluck('label'));

new Chart(document.getElementById('hoursByTeacherChart'), {
    type: 'bar',
    data: {
        labels: teacherLabels,
        datasets: [{
            label: 'Horas Ministradas',
            data: teacherData,
            backgroundColor: '#198754'
        }]
    }
});

const monthData = @json($hoursByMonth->pluck('hours'));
const monthLabels = @json($hoursByMonth->pluck('label'));

new Chart(document.getElementById('hoursByMonthChart'), {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Horas Ministradas por Mês',
            data: monthData,
            borderColor: '#dc3545',
            fill: false
        }]
    }
});
</script>
@endpush
