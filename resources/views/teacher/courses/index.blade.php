@extends('layouts.app')

@section('title', 'Meus Cursos')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Meus Cursos</h3>
            <div class="text-muted">Cursos vinculados as turmas e disciplinas que voce leciona.</div>
        </div>

        <a href="{{ route('teacher.classes') }}" class="btn btn-outline-primary">
            <i class="bi bi-journal-check me-2"></i> Minhas Turmas
        </a>
    </div>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-8 col-lg-6">
            <input
                type="search"
                name="search"
                value="{{ $search }}"
                class="form-control"
                placeholder="Buscar por curso, turma, disciplina ou projeto"
            >
        </div>

        <div class="col-md-2 d-grid">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-search me-2"></i> Filtrar
            </button>
        </div>
    </form>

    <div class="row g-3">
        @forelse($courses as $item)
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap gap-3">
                            <div>
                                <h5 class="fw-bold mb-1">{{ $item['course']->name }}</h5>
                                <div class="text-muted small">
                                    {{ $item['offerings']->count() }} turma(s)
                                    &middot;
                                    {{ $item['disciplines']->count() }} disciplina(s)
                                    &middot;
                                    {{ $item['students_count'] }} aluno(s)
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-lg-5">
                                <div class="text-muted small mb-2">Disciplinas</div>
                                @foreach($item['disciplines'] as $discipline)
                                    <span class="badge bg-primary-subtle text-primary border me-1 mb-1">
                                        {{ $discipline->name }}
                                    </span>
                                @endforeach
                            </div>

                            <div class="col-lg-7">
                                <div class="text-muted small mb-2">Turmas</div>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <tbody>
                                            @foreach($item['offerings'] as $offering)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $offering->name ?? 'Turma '.$offering->id }}</strong>
                                                        <div class="small text-muted">
                                                            {{ $offering->project?->name ?? 'Sem projeto' }}
                                                            @if($offering->unit)
                                                                &middot; {{ $offering->unit->name }}
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="{{ route('teacher.classes.show', $offering) }}" class="btn btn-sm btn-outline-primary">
                                                            Acessar
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border mb-0">
                    Nenhum curso encontrado para os filtros informados.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
