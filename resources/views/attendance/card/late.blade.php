@extends('layouts.app')

@section('title', 'Registros em Atraso')

@section('content')
<div class="container">
    <h3 class="mb-4">
        <i class="bi bi-exclamation-triangle me-2 text-warning"></i> Registros em Atraso
    </h3>

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
