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

    <div class="card shadow-sm">
        <div class="card-body">
            {!! $dataTable->table(['class' => 'table table-striped table-hover w-100'], true) !!}
        </div>
    </div>

</div>

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
@endsection
