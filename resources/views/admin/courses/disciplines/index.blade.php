@extends('layouts.app')

@section('title', 'Disciplinas do Curso')

@section('content')
<div class="container-fluid">

    {{-- CONTEXTO --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Disciplinas do Curso</h4>
        <div class="text-muted">
            Curso selecionado:
            <strong>{{ $course->name }}</strong>
        </div>
    </div>

    {{-- AÇÕES --}}
    <div class="d-flex justify-content-between mb-3">
        <span class="text-muted">
            Marque as disciplinas vinculadas ao curso
        </span>

        <a href="{{ route('admin.disciplines.create') }}"
           class="btn btn-sm btn-outline-primary">
            Nova Disciplina
        </a>
    </div>

    <form method="POST"
          action="{{ route('admin.courses.disciplines.store', $course) }}">
        @csrf

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px;"></th>
                        <th>Disciplina</th>
                        <th>Código</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($disciplines as $discipline)
                        @php
                            $checked = $course->disciplines
                                ->contains($discipline->id);
                        @endphp
                        <tr>
                            <td>
                                <input type="checkbox"
                                       name="disciplines[]"
                                       value="{{ $discipline->id }}"
                                       class="form-check-input"
                                       {{ $checked ? 'checked' : '' }}>
                            </td>
                            <td>{{ $discipline->name }}</td>
                            <td>{{ $discipline->code }}</td>
                            <td>
                                {{ $discipline->active ? 'Ativa' : 'Inativa' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary">
                Salvar alterações
            </button>
        </div>
    </form>

</div>
@endsection
