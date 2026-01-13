@extends('layouts.app')

@section('title', 'Turmas do Curso')

@section('content')
<div class="container-fluid">

    {{-- CONTEXTO --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Turmas do Curso</h4>
        <div class="text-muted">
            Curso selecionado:
            <strong>{{ $course->name }}</strong>
        </div>
    </div>

    {{-- AÇÕES --}}
    <div class="d-flex justify-content-between mb-3">
        <span class="text-muted">
            Turmas cadastradas para este curso
        </span>

        <a href="{{ route('admin.courses.class-offerings.create', $course) }}"
           class="btn btn-sm btn-primary">
            Nova Turma
        </a>
    </div>

    {{-- LISTAGEM --}}
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Identificação</th>
                <th>Período</th>
                <th>Status</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($classOfferings as $offering)
                <tr>
                    <td>{{ $offering->name ?? '—' }}</td>
                    <td>{{ $offering->semester }}/{{ $offering->year }}</td>
                    <td>{{ ucfirst($offering->status) }}</td>
                    <td class="text-end">
                        {{-- aqui entram sessões, bolsistas, etc --}}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-muted">
                        Nenhuma turma cadastrada para este curso.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection
