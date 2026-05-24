@extends('layouts.app')

@section('title', 'Gestão de Projetos')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">
        <i class="bi bi-kanban me-2"></i>
        Gerenciamento de Projetos
    </h3>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg me-2"></i> Criar Novo Projeto
    </a>

    <div class="card shadow-sm">
        <div class="card-body">
            {!! $dataTable->table() !!}
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
