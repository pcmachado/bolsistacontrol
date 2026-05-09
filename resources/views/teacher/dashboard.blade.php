@extends('layouts.app')

@section('title', 'Dashboard do Professor')

@section('content')
<div class="container py-4">
    <div class="dashboard-shell">

        {{-- HERO --}}
        <section class="dashboard-hero">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                <div>
                    <h2 class="fw-bold mb-2">👨‍🏫 {{ $teacher->name }}</h2>
                    <p class="text-muted mb-0">
                        Acompanhe suas aulas, carga horária e desempenho por turma.
                    </p>
                </div>
            </div>
        </section>

        {{-- FILTROS --}}
        <section class="dashboard-grid">

            <div class="card filter-card">
                <div class="card-body">

                    <div class="section-title mb-3">
                        <div>
                            <h5 class="mb-1">Filtros</h5>
                            <small class="text-muted">Refine os dados do dashboard</small>
                        </div>
                    </div>

                    <form method="GET" class="row g-2">

                        <div class="col-md-3">
                            <label>Unidade</label>
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
                            <label>Curso</label>
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
                            <label>De</label>
                            <input type="date" name="filter_from" class="form-control"
                                value="{{ request('filter_from') }}">
                        </div>

                        <div class="col-md-2">
                            <label>Até</label>
                            <input type="date" name="filter_to" class="form-control"
                                value="{{ request('filter_to') }}">
                        </div>

                        <div class="col-md-2 align-self-end">
                            <button class="btn btn-primary w-100">Filtrar</button>
                        </div>

                    </form>
                </div>
            </div>

        </section>

        {{-- KPIs --}}
        <section>

            <div class="section-title">
                <div>
                    <h4 class="mb-1">Resumo</h4>
                    <small class="text-muted">Indicadores gerais</small>
                </div>
            </div>

            <div class="dashboard-grid">

                <x-dashboard.summary-card
                    title="Horas Ministradas"
                    :value="number_format($totalHours,1,',','.')"
                    icon="bi-clock-history"
                />

                <x-dashboard.summary-card
                    title="Aulas"
                    :value="$totalClasses"
                    icon="bi-journal-check"
                />

                <x-dashboard.summary-card
                    title="Cursos"
                    :value="$totalCourses"
                    icon="bi-mortarboard"
                />

                <x-dashboard.summary-card
                    title="Turmas"
                    :value="$totalOfferings"
                    icon="bi-collection"
                />

            </div>
        </section>

        {{-- GRÁFICOS --}}
        <section class="row g-4">

            <div class="col-xl-6">
                <div class="card section-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Horas por Mês</strong>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <canvas id="hoursByMonthChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card section-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Horas por Disciplina</strong>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <canvas id="hoursByDisciplineChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card section-card h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <strong>Horas por Turma</strong>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <canvas id="hoursByOfferingChart"></canvas>
                    </div>
                </div>
            </div>

        </section>

        {{-- TABELA --}}
        <section>

            <div class="section-title">
                <h4 class="mb-0">Últimas Aulas</h4>
            </div>

            <div class="card section-card">
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

        </section>

    </div>
</div>
@endsection