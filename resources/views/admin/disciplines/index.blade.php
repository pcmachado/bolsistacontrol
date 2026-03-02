@extends('layouts.app')

@section('title', 'Disciplinas')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row align-items-start align-items-md-center gap-2 mb-4">
        <h1 class="fw-bold">
            <i class="bi bi-journal-text me-2"></i> Disciplinas
        </h1>

        <a href="{{ route('admin.disciplines.create') }}" class="btn btn-primary px-4">
            <i class="bi bi-plus-lg me-2"></i> Nova Disciplina
        </a>
    </div>

    {{-- FILTRO POR CURSO --}}
    <form method="GET" action="{{ route('admin.disciplines.index') }}" class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Filtrar por Curso</label>
                    <select id="filter_course" name="filter_course" class="form-select">
                        <option value="">Todos</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected(request('filter_course') == $course->id)>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" id="filter-button" class="btn btn-primary w-100">
                        Filtrar
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('admin.disciplines.index') }}" class="btn btn-outline-secondary w-100">
                        Limpar
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- TABELA --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-striped align-middle w-100'], true) !!}
            </div>
        </div>
    </div>

</div>

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush

@endsection
