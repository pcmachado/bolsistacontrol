@extends('layouts.app')

@section('title', 'Aulas da Turma')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">
            <i class="bi bi-calendar-week me-2"></i>
            Aulas da Turma: {{ $offering->name ?? 'Sem nome' }}
        </h2>

        <a href="{{ route('admin.class-offerings.index') }}"
           class="btn btn-outline-secondary px-3">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    {{-- Alerts --}}
    @foreach(['success','warning','error'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg }} shadow-sm">{{ session($msg) }}</div>
        @endif
    @endforeach

    {{-- Form Registrar Aula --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-plus-circle me-2"></i> Registrar Aula
        </div>

        <div class="card-body">
            <form action="{{ route('admin.class-offerings.sessions.store', $offering->id) }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Disciplina</label>
                        <select name="discipline_id" class="form-select" required>
                            @foreach($disciplines as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Professor</label>
                        <select name="teacher_id" class="form-select" required>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Data</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">Início</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">Fim</label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Observações</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>

                <div class="text-end mt-3">
                    <button class="btn btn-primary px-4">
                        <i class="bi bi-check2-circle me-2"></i> Registrar Aula
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Disciplina</label>
                    <select id="filter_discipline" class="form-select">
                        <option value="">Todas</option>
                        @foreach($disciplines as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Professor</label>
                    <select id="filter_teacher" class="form-select">
                        <option value="">Todos</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">De</label>
                    <input type="date" id="filter_from" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Até</label>
                    <input type="date" id="filter_to" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Min. Horas</label>
                    <input type="number" id="filter_min_hours" class="form-control">
                </div>

            </div>
        </div>
    </div>

    {{-- DataTable Aulas --}}
    <div class="card shadow-sm">
        <div class="card-body">
            {!! $dataTable->table(['class' => 'table table-hover table-striped w-100'], true) !!}
        </div>
    </div>

</div>

@push('scripts')
    {!! $dataTable->scripts() !!}
<script>
    const table = window.LaravelDataTables["class-sessions-table"];

    $('#filter_discipline, #filter_teacher').on('change', () => table.draw());
    $('#filter_from, #filter_to, #filter_min_hours').on('keyup change', () => table.draw());
</script>
@endpush

@endsection
