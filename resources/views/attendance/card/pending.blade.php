@extends('layouts.app')

@section('title', 'Registros Pendentes')

@section('content')
<div class="container">
    <h3 class="mb-4"><i class="bi bi-check-circle text-success me-2"></i> Registros Pendentes</h3>
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
