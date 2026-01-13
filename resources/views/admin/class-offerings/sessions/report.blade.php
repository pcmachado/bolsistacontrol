@extends('layouts.app')

@section('title', 'Relatório de Aulas')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="bi bi-file-earmark-bar-graph me-2"></i>
            Relatório de Aulas — {{ $offering->name }}
        </h2>

        <a href="{{ route('admin.class-offerings.sessions.index', $offering->id) }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.class-offerings.sessions.report.pdf', $offering->id) }}"
        class="btn btn-danger me-2">
        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>

        <a href="{{ route('admin.class-offerings.sessions.report.excel', $offering->id) }}"
        class="btn btn-success">
        <i class="bi bi-file-earmark-excel me-1"></i> Excel
        </a>
    </div>

    {{-- FILTROS --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="GET" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">De</label>
                    <input type="date" name="filter_from" class="form-control"
                           value="{{ request('filter_from') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Até</label>
                    <input type="date" name="filter_to" class="form-control"
                           value="{{ request('filter_to') }}">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i> Filtrar
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- RESUMO --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">Resumo Geral</div>
        <div class="card-body">
            <p><strong>Total de Aulas:</strong> {{ $sessions->count() }}</p>
            <p><strong>Total de Horas Ministradas:</strong> {{ number_format($totalHours, 2) }} h</p>
        </div>
    </div>

    {{-- POR DISCIPLINA --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">Horas por Disciplina</div>
        <div class="card-body">

            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Disciplina</th>
                        <th>Aulas</th>
                        <th>Horas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hoursByDiscipline as $row)
                        <tr>
                            <td>{{ $row['discipline'] }}</td>
                            <td>{{ $row['count'] }}</td>
                            <td>{{ number_format($row['hours'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    {{-- POR PROFESSOR --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">Horas por Professor</div>
        <div class="card-body">

            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Professor</th>
                        <th>Aulas</th>
                        <th>Horas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hoursByTeacher as $row)
                        <tr>
                            <td>{{ $row['teacher'] }}</td>
                            <td>{{ $row['count'] }}</td>
                            <td>{{ number_format($row['hours'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    {{-- LISTA DAS AULAS --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold">Aulas Registradas</div>

        <div class="card-body p-0">

            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Disciplina</th>
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
                            <td>{{ $s->discipline->name }}</td>
                            <td>{{ $s->teacher->name }}</td>
                            <td>{{ $s->start_time }}</td>
                            <td>{{ $s->end_time }}</td>
                            <td>{{ number_format($s->duration_hours, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection
