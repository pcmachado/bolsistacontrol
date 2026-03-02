@extends('layouts.app')

@section('title', 'Minhas Submissões')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">
        <i class="bi bi-calendar-check me-2"></i>
        Envio Mensal de Frequência
    </h3>

    {!! $dataTable->table() !!}

</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
