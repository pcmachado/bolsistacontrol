@extends('layouts.app')

@section('title', 'Gestão de Cargos')

@section('content')
<div class="container-fluid">
    <h3 class="mb-0 text-black">
        <i class="bi bi-briefcase me-2"></i>
        Gerenciamento de Cargos/Funções
    </h3>

    <a href="{{ route('admin.positions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i> Criar Novo Cargo
    </a>

    <div class="card shadow-sm">
        <div class="card-body">
            {!! $dataTable->table(['class' => 'table table-hover align-middle table-striped'], true) !!}
        </div>
    </div>
</div>

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
@endsection
