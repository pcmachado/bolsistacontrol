@extends('layouts.app')

@section('title', 'Dashboard Acadêmico')

@section('content')
<div class="container-fluid">

    <h2 class="fw-bold mb-4">
        <i class="bi bi-graph-up-arrow me-2"></i>
        Dashboard Acadêmico Geral
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

        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="fw-bold">{{ number_format($totalHours, 1) }}</h3>
                    <p class="text-muted">Horas Ministradas</p>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="fw-bold">{{ $totalClasses }}</h3>
                    <p class="text-muted">Aulas</p>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="fw-bold">{{ $totalTeachers }}</h3>
                    <p class="text-muted">Docentes</p>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="fw-bold">{{ $totalCourses }}</h3>
                    <p class="text-muted">Cursos Ativos</p>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="fw-bold">{{ $totalOfferings }}</h3>
                    <p class="text-muted">Turmas</p>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="fw-bold">{{ $totalStudents }}</h3>
                    <p class="text-muted">Bolsistas</p>
                </div>
            </div>
        </div>

    </div>

    {{-- GRÁFICO 1 — Horas por mês --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold bg-white">
            <i class="bi bi-calendar-range me-2"></i>
            Horas Ministradas por Mês
        </div>
        <div class="card-body">
            <canvas id="hoursByMonthChart"></canvas>
        </div>
    </div>

    {{-- GRÁFICO 2 — Horas por curso --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold bg-white">
            <i class="bi bi-journal-bookmark me-2"></i>
            Horas por Curso
        </div>
        <div class="card-body">
            <canvas id="hoursByCourseChart"></canvas>
        </div>
    </div>

    {{-- GRÁFICO 3 — Horas por professor --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold bg-white">
            <i class="bi bi-person-lines-fill me-2"></i>
            Horas por Professor
        </div>
        <div class="card-body">
            <canvas id="hoursByTeacherChart"></canvas>
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

// COURSE
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

// TEACHER
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
</script>
@endpush
