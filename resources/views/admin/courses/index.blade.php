@extends('layouts.app')

@section('title', 'Cursos')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="fw-bold"><i class="bi bi-journal-bookmark me-2"></i> Cursos</h1>

        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary px-4">
            <i class="bi bi-plus-lg me-2"></i> Novo Curso
        </a>
    </div>

    {{-- FILTROS --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Unidade</label>
                    <select id="filter_unit" class="form-select">
                        <option value="">Todas</option>
                        @foreach(\App\Models\Unit::orderBy('name')->get() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Projeto</label>
                    <select id="filter_project" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Project::orderBy('name')->get() as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>
    </div>

    {{-- TABELA --}}
    <div class="card shadow-sm">
        <div class="card-body">
            {!! $dataTable->table(['class' => 'table table-striped table-hover w-100 align-middle'], true) !!}
        </div>
    </div>

</div>

@push('scripts')
    {!! $dataTable->scripts() !!}

    <script>
        const table = window.LaravelDataTables["courses-table"];

        $('#filter_unit, #filter_project').on('change', function() {
            table.draw();
        });
    </script>
@endpush
@endsection
