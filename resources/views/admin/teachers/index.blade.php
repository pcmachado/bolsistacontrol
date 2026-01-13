@extends('layouts.app')

@section('title', 'Professores')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold"><i class="bi bi-person-video3 me-2"></i> Professores</h1>

        <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary px-4">
            <i class="bi bi-plus-lg me-2"></i> Novo Professor
        </a>
    </div>

    {{-- Filtros --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Unidade</label>
                    <select id="filter_unit" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Unit::orderBy('name')->get() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Curso</label>
                    <select id="filter_course" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Course::orderBy('name')->get() as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Turma</label>
                    <select id="filter_offering" class="form-select">
                        <option value="">Todas</option>
                        @foreach(\App\Models\ClassOffering::orderBy('name')->get() as $co)
                            <option value="{{ $co->id }}">{{ $co->name ?? "Turma #$co->id" }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

        </div>
    </div>

    {{-- Tabela --}}
    <div class="card shadow-sm">
        <div class="card-body">
            {!! $dataTable->table(['class' => 'table table-hover align-middle w-100'], true) !!}
        </div>
    </div>

</div>

@push('scripts')
{!! $dataTable->scripts() !!}

<script>
    const table = window.LaravelDataTables["admin.teachers-table"];

    $('#filter_unit, #filter_course, #filter_offering').on('change', function () {
        table.draw();
    });
</script>
@endpush
@endsection
