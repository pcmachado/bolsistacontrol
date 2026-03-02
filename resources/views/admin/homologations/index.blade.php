@extends('layouts.app')

@section('title', 'Homologações de Frequência')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">
        <i class="bi bi-calendar-check me-2"></i>
        Homologações de Frequência
    </h3>
    @include('admin.homologations.partials.filters')

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                {!! $dataTable->table() !!}
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
