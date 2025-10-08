@extends('layouts.app')

@section('title', 'Gestão de Cargos')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Gerenciamento de Cargos</h1>
        <a href="{{ route('admin.positions.create') }}" class="btn btn-primary shadow-sm rounded-0">
            <i class="bi bi-plus-lg me-2"></i> Criar Novo Cargo
        </a>
        <div class="card shadow-sm">
            <div class="card-body">
                {!! $dataTable->table(['class' => 'table table-hover table-striped table-bordered w-100'], true) !!}
            </div>
        </div>
</div>

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
@endsection