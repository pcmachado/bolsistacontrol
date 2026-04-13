@extends('layouts.app')

@section('title', 'Professores')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row align-items-start align-items-md-center gap-2 mb-4">
        <h1 class="fw-bold"><i class="bi bi-person-video3 me-2"></i> Professores</h1>

        <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary px-4">
            <i class="bi bi-plus-lg me-2"></i> Novo Professor
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.teachers.index') }}" class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Unidade</label>
                    <select id="filter_unit" name="filter_unit" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Unit::orderBy('name')->get() as $u)
                            <option value="{{ $u->id }}" @selected(request('filter_unit') == $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Curso</label>
                    <select id="filter_course" name="filter_course" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Course::orderBy('name')->get() as $c)
                            <option value="{{ $c->id }}" @selected(request('filter_course') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Turma</label>
                    <select id="filter_offering" name="filter_offering" class="form-select">
                        <option value="">Todas</option>
                        @foreach($offerings as $co)
                            <option value="{{ $co->id }}" @selected(request('filter_offering') == $co->id)>{{ $co->name ?? "Turma #$co->id" }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary w-100">Limpar</a>
                </div>

            </div>

        </div>
    </form>

    {{-- Tabela --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-hover align-middle w-100'], true) !!}
            </div>
        </div>
    </div>

</div>

@push('scripts')
{!! $dataTable->scripts() !!}
@endpush
@endsection
