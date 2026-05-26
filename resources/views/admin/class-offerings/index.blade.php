@extends('layouts.app')

@section('title', 'Turmas')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row align-items-start align-items-md-center gap-2 mb-4">
        <h1 class="fw-bold"><i class="bi bi-collection me-2"></i> Turmas</h1>

        <a href="{{ route('admin.class-offerings.create') }}" class="btn btn-primary px-4">
            <i class="bi bi-plus-lg me-2"></i> Nova Turma
        </a>
    </div>

    <form method="GET" action="{{ route('admin.class-offerings.index') }}" class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-funnel me-2"></i> Filtros
        </div>

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Curso</label>
                    <select id="filter_course" name="filter_course" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Course::orderBy('name')->get() as $c)
                            <option value="{{ $c->id }}" @selected(request('filter_course') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Unidade</label>
                    <select id="filter_unit" name="filter_unit" class="form-select">
                        <option value="">Todas</option>
                        @foreach(\App\Models\Unit::orderBy('name')->get() as $u)
                            <option value="{{ $u->id }}" @selected(request('filter_unit') == $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Projeto</label>
                    <select id="filter_project" name="filter_project" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Project::orderBy('name')->get() as $p)
                            <option value="{{ $p->id }}" @selected(request('filter_project') == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select id="filter_status" name="filter_status" class="form-select">
                        <option value="">Todos</option>
                        <option value="planned" @selected(request('filter_status') === 'planned')>Planejado</option>
                        <option value="ongoing" @selected(request('filter_status') === 'ongoing')>Em andamento</option>
                        <option value="finished" @selected(request('filter_status') === 'finished')>Concluido</option>
                        <option value="cancelled" @selected(request('filter_status') === 'cancelled')>Cancelado</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Ano</label>
                    <input id="filter_year" name="filter_year" type="number" class="form-control" value="{{ request('filter_year') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Semestre</label>
                    <input id="filter_semester" name="filter_semester" type="text" class="form-control" placeholder="2025/1" value="{{ request('filter_semester') }}">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('admin.class-offerings.index') }}" class="btn btn-outline-secondary w-100">Limpar</a>
                </div>

            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-striped table-hover w-100'], true) !!}
            </div>
        </div>
    </div>

</div>

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
@endsection
