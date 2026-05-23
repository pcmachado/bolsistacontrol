@extends('layouts.app')

@section('title', 'Gestão de Cargos')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
        <h1 class="mb-0 text-black">Gerenciamento de Cargos/Funções</h1>

        <a href="{{ route('admin.positions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i> Criar Novo Cargo
        </a>
    </div>

    <div class="card shadow-sm rounded-4 border-0 overflow-hidden">
        <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-hover align-middle table-striped w-100 mb-0'], true) !!}
            </div>
    </div>
</div>

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
@endsection
