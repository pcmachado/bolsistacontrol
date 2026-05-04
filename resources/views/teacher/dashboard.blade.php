@extends('layouts.app')

@section('title', 'Dashboard do Professor')

@section('content')
<div class="container-fluid">

    {{-- HERO --}}
    <div class="dashboard-hero mb-4">
        <h2 class="fw-bold mb-1">
            👨‍🏫 Dashboard do Professor
        </h2>
        <p class="text-muted mb-0">
            {{ $teacher->name }}
        </p>
    </div>

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
                        🔍 Filtrar
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <x-dashboard.summary-card
                title="Horas Ministradas"
                :value="number_format($totalHours, 1)"
                icon="bi-clock-history"
            />
        </div>

        <div class="col-md-3">
            <x-dashboard.summary-card
                title="Aulas Ministradas"
                :value="$totalClasses"
                icon="bi-journal-check"
            />
        </div>

        <div class="col-md-3">
            <x-dashboard.summary-card
                title="Cursos"
                :value="$totalCourses"
                icon="bi-mortarboard"
            />
        </div>

        <div class="col-md-3">
            <x-dashboard.summary-card
                title="Turmas"
                :value="$totalOfferings"
                icon="bi-collection"
            />
        </div>

    </div>

    {{-- GRÁFICOS --}}
    <div class="row g-3">

        <div class="col-md-6">
            <x-dashboard.chart-card title="Horas por Mês" id="hoursByMonthChart" />
        </div>

        <div class="col-md-6">
            <x-dashboard.chart-card title="Horas por Disciplina" id="hoursByDisciplineChart" />
        </div>

        <div class="col-md-12">
            <x-dashboard.chart-card title="Horas por Turma" id="hoursByOfferingChart" />
        </div>

    </div>

    {{-- TABELA --}}
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white fw-semibold">
            Últimas Aulas
        </div>

        <div class="table-responsive">
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
                    @forelse($recent as $s)
                        <tr>
                            <td>{{ $s->date->format('d/m/Y') }}</td>
                            <td>{{ $s->discipline->name }}</td>
                            <td>{{ $s->classOffering->name }}</td>
                            <td>{{ number_format($s->duration_hours, 1) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Nenhuma aula encontrada
                            </td>
                        </tr>
                    @endforelse
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
