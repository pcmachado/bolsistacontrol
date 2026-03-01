@extends('layouts.app')

@section('title', 'Turmas')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold"><i class="bi bi-collection me-2"></i> Turmas</h1>

        <a href="{{ route('admin.class-offerings.create') }}" class="btn btn-primary px-4">
            <i class="bi bi-plus-lg me-2"></i> Nova Turma
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-funnel me-2"></i> Filtros
        </div>

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Curso</label>
                    <select id="filter_course" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Course::orderBy('name')->get() as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Unidade</label>
                    <select id="filter_unit" class="form-select">
                        <option value="">Todas</option>
                        @foreach(\App\Models\Unit::orderBy('name')->get() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Projeto</label>
                    <select id="filter_project" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Project::orderBy('name')->get() as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select id="filter_status" class="form-select">
                        <option value="">Todos</option>
                        <option value="planned">Planejado</option>
                        <option value="ongoing">Em andamento</option>
                        <option value="finished">Concluído</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Ano</label>
                    <input id="filter_year" type="number" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Semestre</label>
                    <input id="filter_semester" type="text" class="form-control" placeholder="2025/1">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Mín. Bolsistas</label>
                    <input id="filter_min_students" type="number" class="form-control">
                </div>

            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            {!! $dataTable->table(['class' => 'table table-striped table-hover w-100'], true) !!}
        </div>
    </div>

</div>

@push('scripts')
    {!! $dataTable->scripts() !!}

    <script>
        const table = window.LaravelDataTables["class-offerings-table"];

        $('#filter_course, #filter_unit, #filter_project, #filter_status').on('change', () => table.draw());
        $('#filter_year, #filter_semester, #filter_min_students').on('keyup change', () => table.draw());
    </script>
@endpush
@endsection
