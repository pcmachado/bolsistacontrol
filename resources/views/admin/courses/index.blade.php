@extends('layouts.app')

@section('title', 'Gestão de Cursos')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold mb-0">
            <i class="bi bi-journal-bookmark me-2"></i> Gerenciamento de Cursos
        </h1>
        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary shadow-sm px-4">
            <i class="bi bi-plus-lg me-2"></i> Novo Curso
        </a>
    </div>

    {{-- CARD DE FILTROS (Opcional) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <span class="fw-semibold">
                <i class="bi bi-funnel me-2"></i> Filtros
            </span>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filtersBox">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        <div class="collapse" id="filtersBox">
            <div class="card-body row g-3">

                {{-- Filtro por nome --}}
                <div class="col-md-4">
                    <label class="form-label">Pesquisar por Nome</label>
                    <input type="text" id="filter_name" class="form-control" placeholder="Digite o nome...">
                </div>

                {{-- Filtro por unidade (se estiver usando project_course_unit) --}}
                <div class="col-md-4">
                    <label class="form-label">Unidade</label>
                    <select id="filter_unit" class="form-select">
                        <option value="">Todas</option>
                        @foreach(\App\Models\Unit::orderBy('name')->get() as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Botão limpar --}}
                <div class="col-md-4 d-flex align-items-end">
                    <button id="resetFilters" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-lg me-1"></i> Limpar Filtros
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- CARD DA TABELA --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="m-0 fw-semibold">
                <i class="bi bi-table me-2"></i> Lista de Cursos
            </h5>
        </div>

        <div class="card-body">
            {!! $dataTable->table(['class' => 'table table-hover table-striped align-middle w-100'], true) !!}
        </div>
    </div>

</div>

@push('scripts')
    {!! $dataTable->scripts() !!}

    <script>
        // Atualiza automaticamente quando filtro muda
        $('#filter_name, #filter_unit').on('keyup change', function () {
            window.LaravelDataTables["courses-table"].draw();
        });

        // Resetar filtros
        $('#resetFilters').click(function () {
            $('#filter_name').val('');
            $('#filter_unit').val('');
            window.LaravelDataTables["courses-table"].draw();
        });
    </script>
@endpush
@endsection
