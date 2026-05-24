@extends('layouts.app')

@section('title', 'Gestão de Instituições')

@section('content')

<div class="container-fluid">
    <h1 class="mb-4">Gerenciamento de Instituições</h1>
        <a href="{{ route('admin.institutions.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-2"></i> Criar Nova Instituição
        </a>
        <div class="card shadow-sm">
            <div class="card-body">
                {!! $dataTable->table(['class' => 'table table-hover table-striped table-bordered w-100'], true) !!}
            </div>
        </div>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush