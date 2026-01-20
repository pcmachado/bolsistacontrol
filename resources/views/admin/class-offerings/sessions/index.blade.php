@extends('layouts.app')

@section('title', 'Aulas da Turma')

@section('content')
<div class="container-fluid">

    {{-- CONTEXTO --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Aulas da Turma</h4>
        <div class="text-muted">
            Curso: <strong>{{ $offering->course->name }}</strong><br>
            Turma: <strong>{{ $offering->name }}</strong>
        </div>
    </div>

    {{-- NOVA AULA --}}
    <form method="POST"
          action="{{ route('admin.class-offerings.sessions.store', $offering) }}"
          class="card mb-4">
        @csrf
        <div class="card-body row g-3">

            <div class="col-md-3">
                <label class="form-label">Disciplina</label>
                <select name="discipline_id" class="form-select" required>
                    <option value="">Selecione</option>
                    @foreach($disciplines as $discipline)
                        <option value="{{ $discipline->id }}">
                            {{ $discipline->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Data</label>
                <input type="date" name="date" class="form-control" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Início</label>
                <input type="time" name="start_time" class="form-control" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Fim</label>
                <input type="time" name="end_time" class="form-control" required>
            </div>

            <div class="col-md-1">
                <label class="form-label">CH</label>
                <input type="number" name="workload" class="form-control" min="1" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Sala</label>
                <input type="text" name="room" class="form-control">
            </div>

            <div class="col-12">
                <label class="form-label">Observações</label>
                <textarea name="notes" class="form-control sgb-textarea" rows="2"></textarea>
            </div>

            <div class="col-12 text-end">
                <button class="btn btn-primary">
                    Registrar Aula
                </button>
            </div>
        </div>
    </form>

    {{-- LISTAGEM --}}
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Data</th>
                <th>Disciplina</th>
                <th>Horário</th>
                <th>CH</th>
                <th>Sala</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $session)
                <tr>
                    <td>{{ formatDate($session->date) }}</td>
                    <td>{{ $session->discipline->name }}</td>
                    <td>{{ $session->start_time }} – {{ $session->end_time }}</td>
                    <td>{{ $session->workload }}</td>
                    <td>{{ $session->room }}</td>
                    <td class="text-end">
                        <form method="POST"
                              action="{{ route('admin.class-offerings.sessions.destroy', [$offering, $session]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-muted text-center">
                        Nenhuma aula registrada
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection
