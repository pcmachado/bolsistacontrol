@extends('layouts.app')

@section('title', 'Gestão de Projetos')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Gerenciamento de Projetos</h1>
        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary shadow-sm rounded-0">
            <i class="bi bi-plus-lg me-2"></i> Criar Novo Projeto
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