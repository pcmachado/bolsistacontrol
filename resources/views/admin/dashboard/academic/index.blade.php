@extends('layouts.app')

@section('title', 'Dashboard Acadêmico')

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
        font-size: 1.85rem;
        font-weight: 700;
        line-height: 1.05;
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

    .tone-dark {
        background: rgba(33, 37, 41, 0.1);
        color: #212529;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-2">
    <div class="dashboard-shell">
        <section class="dashboard-hero">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                <div>
                    <h1 class="h3 fw-bold mb-2">Dashboard Acadêmico</h1>
                    <p class="text-muted mb-0">
                        Consolide aulas, carga horária, professores, cursos e turmas com filtros por unidade, curso, professor e intervalo.
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard admin
                    </a>
                    <a href="{{ route('admin.class-offerings.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-collection me-1"></i> Turmas
                    </a>
                </div>
            </div>
        </section>

        <section class="card filter-card">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Unidade</label>
                        <select name="filter_unit" class="form-select">
                            <option value="">Todas</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" @selected(request('filter_unit') == $unit->id)>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Curso</label>
                        <select name="filter_course" class="form-select">
                            <option value="">Todos</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" @selected(request('filter_course') == $course->id)>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Professor</label>
                        <select name="filter_teacher" class="form-select">
                            <option value="">Todos</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected(request('filter_teacher') == $teacher->id)>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">De</label>
                        <input type="date" name="filter_from" value="{{ request('filter_from') }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Até</label>
                        <input type="date" name="filter_to" value="{{ request('filter_to') }}" class="form-control">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary flex-grow-1">
                            <i class="bi bi-funnel me-1"></i> Aplicar filtros
                        </button>
                        <a href="{{ route('admin.dashboard.academic') }}" class="btn btn-outline-secondary">Limpar</a>
                    </div>
                </form>
            </div>
        </section>

        <section class="dashboard-grid">
            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Carga horária</div>
                            <div class="value">{{ number_format($totalHours, 1, ',', '.') }}h</div>
                            <small class="text-muted">Horas das aulas filtradas</small>
                        </div>
                        <span class="icon tone-primary"><i class="bi bi-stopwatch"></i></span>
                    </div>
                </div>
            </div>

            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Aulas</div>
                            <div class="value">{{ $totalClasses }}</div>
                            <small class="text-muted">Sessões localizadas</small>
                        </div>
                        <span class="icon tone-info"><i class="bi bi-calendar3"></i></span>
                    </div>
                </div>
            </div>

            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Professores</div>
                            <div class="value">{{ $totalTeachers }}</div>
                            <small class="text-muted">Professores envolvidos</small>
                        </div>
                        <span class="icon tone-success"><i class="bi bi-person-badge"></i></span>
                    </div>
                </div>
            </div>

            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Cursos</div>
                            <div class="value">{{ $totalCourses }}</div>
                            <small class="text-muted">Cursos com atividade</small>
                        </div>
                        <span class="icon tone-warning"><i class="bi bi-mortarboard"></i></span>
                    </div>
                </div>
            </div>

            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Turmas</div>
                            <div class="value">{{ $totalOfferings }}</div>
                            <small class="text-muted">Ofertas cadastradas</small>
                        </div>
                        <span class="icon tone-dark"><i class="bi bi-collection"></i></span>
                    </div>
                </div>
            </div>

            <div class="card summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">Bolsistas</div>
                            <div class="value">{{ $totalStudents }}</div>
                            <small class="text-muted">Bolsistas cadastrados</small>
                        </div>
                        <span class="icon tone-danger"><i class="bi bi-people"></i></span>
                    </div>
                </div>
            </div>

            <div class="card summary-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-2">
                                Risco Acadêmico
                            </div>
                            <div class="value">
                                {{ $riskSummary['critical'] ?? 0 }}
                            </div>
                            <small class="text-muted">
                                alunos em nível crítico
                            </small>
                        </div>
                        <span class="icon tone-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </span>
                    </div>
                    {{-- mini breakdown --}}
                    <div class="mt-3 small">
                        <div>🔴 Crítico: {{ $riskSummary['critical'] ?? 0 }}</div>
                        <div>🟠 Risco: {{ $riskSummary['risk'] ?? 0 }}</div>
                        <div>🟡 Atenção: {{ $riskSummary['warning'] ?? 0 }}</div>
                    </div>
                    <a href="{{ route('admin.dashboard.risk') }}"
                    class="btn btn-sm btn-outline-danger mt-3 w-100">
                        Ver monitoramento →
                    </a>
                </div>

            </div>
        </section>

        <section class="row g-4">
            <div class="col-xl-4">
                <div class="card section-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Carga horária por mês</strong>
                        <div class="text-muted small">Evolução temporal das aulas.</div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <canvas id="hoursByMonthChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card section-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Carga horária por curso</strong>
                        <div class="text-muted small">Distribuição entre os cursos filtrados.</div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <canvas id="hoursByCourseChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card section-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Carga horária por professor</strong>
                        <div class="text-muted small">Participação do professor no recorte atual.</div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <canvas id="hoursByTeacherChart"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const hoursByMonth = @json(array_values($hoursByMonth->toArray()));
const hoursByCourse = @json(array_values($hoursByCourse->toArray()));
const hoursByTeacher = @json(array_values($hoursByTeacher->toArray()));

new Chart(document.getElementById('hoursByMonthChart'), {
    type: 'line',
    data: {
        labels: hoursByMonth.map(item => item.label),
        datasets: [{
            label: 'Horas',
            data: hoursByMonth.map(item => item.hours),
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.16)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        plugins: { legend: { display: false } }
    }
});

new Chart(document.getElementById('hoursByCourseChart'), {
    type: 'bar',
    data: {
        labels: hoursByCourse.map(item => item.label),
        datasets: [{
            label: 'Horas',
            data: hoursByCourse.map(item => item.hours),
            backgroundColor: '#198754'
        }]
    },
    options: {
        plugins: { legend: { display: false } }
    }
});

new Chart(document.getElementById('hoursByTeacherChart'), {
    type: 'doughnut',
    data: {
        labels: hoursByTeacher.map(item => item.label),
        datasets: [{
            data: hoursByTeacher.map(item => item.hours),
            backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#20c997', '#fd7e14']
        }]
    },
    options: {
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
@endpush
