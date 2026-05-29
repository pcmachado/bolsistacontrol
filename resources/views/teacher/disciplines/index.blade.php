@extends('layouts.app')

@section('title', 'Minhas Disciplinas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Minhas Disciplinas</h3>
            <div class="text-muted">Disciplinas vinculadas as suas turmas como professor.</div>
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
                placeholder="Buscar por disciplina, curso, turma ou projeto"
            >
        </div>

        <div class="col-md-2 d-grid">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-search me-2"></i> Filtrar
            </button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Disciplina</th>
                    <th>Cursos</th>
                    <th>Turmas</th>
                    <th class="text-center">Horas</th>
                    <th class="text-center">Dias/Aulas</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($disciplines as $item)
                    <tr>
                        <td>
                            <strong>{{ $item['discipline']->name }}</strong>
                            <div class="small text-muted">
                                {{ $item['projects']->pluck('name')->join(', ') ?: 'Sem projeto' }}
                            </div>
                        </td>
                        <td>
                            @foreach($item['courses'] as $course)
                                <span class="badge bg-secondary-subtle text-secondary border me-1 mb-1">
                                    {{ $course->name }}
                                </span>
                            @endforeach
                        </td>
                        <td>
                            @foreach($item['offerings'] as $offering)
                                <a href="{{ route('teacher.classes.show', [$offering, 'discipline_id' => $item['discipline']->id]) }}" class="badge bg-primary-subtle text-primary border text-decoration-none me-1 mb-1">
                                    {{ $offering->name ?? 'Turma '.$offering->id }}
                                </a>
                            @endforeach
                        </td>
                        <td class="text-center">
                            {{ number_format($item['planned_hours'], 1, ',', '.') }}h
                        </td>
                        <td class="text-center">
                            {{ $item['planned_days'] }}
                        </td>
                        <td class="text-end">
                            @if($item['offerings']->isNotEmpty())
                                <a href="{{ route('teacher.classes.show', [$item['offerings']->first(), 'discipline_id' => $item['discipline']->id]) }}" class="btn btn-sm btn-outline-primary">
                                    Acessar
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Nenhuma disciplina encontrada para os filtros informados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
