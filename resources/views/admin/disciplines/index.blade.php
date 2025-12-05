@extends('layouts.app')

@section('title', 'Disciplinas')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">
            <i class="bi bi-journal-text me-2"></i> Disciplinas
        </h1>

        <a href="{{ route('admin.disciplines.create') }}" class="btn btn-primary px-4">
            <i class="bi bi-plus-lg me-2"></i> Nova Disciplina
        </a>
    </div>

    {{-- FILTRO POR CURSO --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Filtrar por Curso</label>
                    <select id="filter_course" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Course::orderBy('name')->get() as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button id="resetFilters" class="btn btn-outline-secondary w-100">
                        Limpar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- TABELA --}}
    <div class="card shadow-sm">
        <div class="card-body">
            {!! $dataTable->table(['class' => 'table table-striped align-middle w-100'], true) !!}
        </div>
    </div>

</div>

@push('scripts')
    {!! $dataTable->scripts() !!}

    <script>
        // Recarregar DataTable quando filtro muda
        $('#filter_course').on('change', function () {
            window.LaravelDataTables["disciplines-table"].draw();
        });

        $('#resetFilters').on('click', function () {
            $('#filter_course').val('');
            window.LaravelDataTables["disciplines-table"].draw();
        });
    </script>
@endpush

@endsection
