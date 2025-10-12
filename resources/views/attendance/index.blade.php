@extends('layouts.app')

@section('title', 'Meus Registros de Frequência')

@section('content')
<div class="container">
    <h3 class="mb-4"><i class="bi bi-clock-history me-2"></i> Meus Registros de Frequência</h3>

    {{-- Botão para criar novo registro --}}
    <div class="mb-3">
        <a href="{{ route('attendance.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Novo Registro
        </a>
    </div>

    {{-- DataTable --}}
    <div class="card shadow-sm">
        <div class="card-body">
            {!! $dataTable->table(['class' => 'table table-striped table-bordered align-middle'], true) !!}
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
