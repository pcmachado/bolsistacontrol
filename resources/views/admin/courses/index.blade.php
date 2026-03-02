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

    @include('admin.courses.partials.filters')

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
