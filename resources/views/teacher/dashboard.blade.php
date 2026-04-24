@extends('layouts.app')

@section('title', 'Dashboard do Professor')

@section('content')
<div class="container-fluid">

    <h2 class="fw-bold mb-4">
        <i class="bi bi-person-badge me-2"></i>
        Dashboard — {{ $teacher->name }}
    </h2>

    {{-- FILTROS --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Unidade</label>
                    <select name="filter_unit" class="form-select">
                        <option value="">Todas</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" @selected(request('filter_unit') == $u->id)>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Curso</label>
                    <select name="filter_course" class="form-select">
                        <option value="">Todos</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" @selected(request('filter_course') == $c->id)>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">De</label>
                    <input type="date" name="filter_from" class="form-control"
                        value="{{ request('filter_from') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Até</label>
                    <input type="date" name="filter_to" class="form-control"
                        value="{{ request('filter_to') }}">
                </div>

                <div class="col-md-2 align-self-end">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i> Filtrar
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row mb-4 text-center">

        <div class="col-md-3">
            <x-kpi-card title="Horas Ministradas" value="{{ number_format($totalHours, 1) }}" />
        </div>

        <div class="col-md-3">
            <x-kpi-card title="Aulas Ministradas" value="{{ $totalClasses }}" />
        </div>

        <div class="col-md-3">
            <x-kpi-card title="Cursos" value="{{ $totalCourses }}" />
        </div>

        <div class="col-md-3">
            <x-kpi-card title="Turmas" value="{{ $totalOfferings }}" />
        </div>

    </div>

    {{-- GRÁFICO — Horas por mês --}}
    <x-chart-card title="Horas por Mês" id="hoursByMonthChart" />

    {{-- GRÁFICO — Horas por disciplina --}}
    <x-chart-card title="Horas por Disciplina" id="hoursByDisciplineChart" />

    {{-- GRÁFICO — Horas por Turma --}}
    <x-chart-card title="Horas por Turma" id="hoursByOfferingChart" />

    {{-- Últimas aulas --}}
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-clock-history me-2"></i> Últimas Aulas
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Disciplina</th>
                        <th>Turma</th>
                        <th>Horas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent as $s)
                    <tr>
                        <td>{{ $s->date->format('d/m/Y') }}</td>
                        <td>{{ $s->discipline->name }}</td>
                        <td>{{ $s->classOffering->name }}</td>
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
// MONTH
new Chart(document.getElementById('hoursByMonthChart'), {
    type: 'line',
    data: {
        labels: @json($hoursByMonth->pluck('label')),
        datasets: [{
            label: 'Horas',
            data: @json($hoursByMonth->pluck('hours')),
            borderColor: '#0d6efd',
            fill: false
        }]
    }
});

// DISCIPLINE
new Chart(document.getElementById('hoursByDisciplineChart'), {
    type: 'bar',
    data: {
        labels: @json($hoursByDiscipline->pluck('label')),
        datasets: [{
            label: 'Horas',
            data: @json($hoursByDiscipline->pluck('hours')),
            backgroundColor: '#198754'
        }]
    }
});

// OFFERING
new Chart(document.getElementById('hoursByOfferingChart'), {
    type: 'bar',
    data: {
        labels: @json($hoursByOffering->pluck('label')),
        datasets: [{
            label: 'Horas',
            data: @json($hoursByOffering->pluck('hours')),
            backgroundColor: '#dc3545'
        }]
    }
});
</script>
@endpush
