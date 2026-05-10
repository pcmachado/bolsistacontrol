@extends('layouts.app')

@section('title', 'Minhas SubmissÃµes')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">
        <i class="bi bi-calendar-check me-2"></i>
        Envio Mensal de FrequÃªncia
    </h3>

    @include('attendance.partials.project-tabs')

    @include('attendance.submissions.partials.filters')

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
