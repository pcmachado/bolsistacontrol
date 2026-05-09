@extends('layouts.app')

@section('title', 'Minhas Turmas')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <h4 class="mb-4">Minhas Turmas</h4>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="search"
                    name="search"
                    value="{{ old('search', $search ?? '') }}"
                    class="form-control"
                    placeholder="Buscar por turma, curso, disciplina, projeto ou aluno">
            </div>

            <div class="col-md-2">
                <select name="course_id" class="form-select">
                    <option value="">Todos os cursos</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected($courseId === $course->id)>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="project_id" class="form-select">
                    <option value="">Todos os projetos</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" @selected($projectId === $project->id)>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="discipline_id" class="form-select">
                    <option value="">Todas as disciplinas</option>
                    @foreach($disciplines as $discipline)
                        <option value="{{ $discipline->id }}" @selected($disciplineId === $discipline->id)>
                            {{ $discipline->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Turma</th>
                        <th>Curso</th>
                        <th>Projeto</th>
                        <th>Disciplinas</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($classes as $c)
                        <tr>
                            <td>{{ $c->name ?? 'Turma '.$c->id }}</td>
                            <td>{{ $c->course?->name ?? '-' }}</td>
                            <td>{{ $c->project?->name ?? '-' }}</td>
                            <td>
                                @foreach($c->disciplines as $d)
                                    <span class="badge bg-primary me-1 mb-1">
                                        {{ $d->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('teacher.classes.show', $c->id) }}"
                                   class="btn btn-primary btn-sm">
                                    Acessar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                Nenhuma turma encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection