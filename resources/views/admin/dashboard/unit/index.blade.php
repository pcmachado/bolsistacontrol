@extends('layouts.app')

@section('title', 'Dashboard da Unidade')

@section('content')
<div class="container-fluid">

    <h2 class="fw-bold mb-4">
        <i class="bi bi-building me-2"></i>
        Dashboard — Unidade {{ $unit->name }}
    </h2>

    {{-- FILTROS --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="GET" class="row g-3">

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

                <div class="col-md-3">
                    <label class="form-label">Professor</label>
                    <select name="filter_teacher" class="form-select">
                        <option value="">Todos</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" @selected(request('filter_teacher') == $t->id)>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Turma</label>
                    <select name="filter_offering" class="form-select">
                        <option value="">Todas</option>
                        @foreach($offerings as $o)
                            <option value="{{ $o->id }}" @selected(request('filter_offering') == $o->id)>
                                {{ $o->name ?? "Turma ".$o->id }}
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
    <div class="row text-center mb-4">
        <x-kpi-card title="Horas Ministradas" value="{{ number_format($totalHours, 1) }}" class="col-md-2" />
        <x-kpi-card title="Aulas" value="{{ $totalClasses }}" class="col-md-2" />
        <x-kpi-card title="Docentes" value="{{ $totalTeachers }}" class="col-md-2" />
        <x-kpi-card title="Cursos Ativos" value="{{ $totalCourses }}" class="col-md-2" />
        <x-kpi-card title="Turmas" value="{{ $totalOfferings }}" class="col-md-2" />
        <x-kpi-card title="Bolsistas" value="{{ $totalStudents }}" class="col-md-2" />
    </div>

    {{-- GRÁFICO — Horas por Mês --}}
    <x-chart-card id="hoursByMonthChart" title="Horas por Mês" />

    {{-- GRÁFICO — Horas por Curso --}}
    <x-chart-card id="hoursByCourseChart" title="Horas por Curso" />

    {{-- GRÁFICO — Horas por Professor --}}
    <x-chart-card id="hoursByTeacherChart" title="Horas por Professor" />

    {{-- GRÁFICO — Horas por Turma --}}
    <x-chart-card id="hoursByOfferingChart" title="Horas por Turma" />

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
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

new Chart(document.getElementById('hoursByCourseChart'), {
    type: 'bar',
    data: {
        labels: @json($hoursByCourse->pluck('label')),
        datasets: [{
            label: 'Horas',
            data: @json($hoursByCourse->pluck('hours')),
            backgroundColor: '#198754'
        }]
    }
});

new Chart(document.getElementById('hoursByTeacherChart'), {
    type: 'bar',
    data: {
        labels: @json($hoursByTeacher->pluck('label')),
        datasets: [{
            label: 'Horas',
            data: @json($hoursByTeacher->pluck('hours')),
            backgroundColor: '#dc3545'
        }]
    }
});

new Chart(document.getElementById('hoursByOfferingChart'), {
    type: 'bar',
    data: {
        labels: @json($hoursByOffering->pluck('label')),
        datasets: [{
            label: 'Horas',
            data: @json($hoursByOffering->pluck('hours')),
            backgroundColor: '#0d6efd'
        }]
    }
});
</script>
@endpush
