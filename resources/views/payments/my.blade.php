@extends('layouts.app')

@section('title', 'Meus Pagamentos')

@section('content')
<div class="container-fluid">

    <h1 class="mb-4">Meus Pagamentos</h1>

    {{-- ============================= --}}
    {{-- FILTROS --}}
    {{-- ============================= --}}
    <form method="GET" class="row g-2 mb-3 align-items-end">

        <div class="col-md-3">
            <label class="form-label">Competência</label>
            <input 
                type="month" 
                name="month" 
                value="{{ request('month', now()->format('Y-m')) }}" 
                class="form-control">
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>

        <div class="col-md-2">
            <a href="{{ route('payments.my') }}" class="btn btn-outline-secondary w-100">
                Limpar
            </a>
        </div>

        <div class="col-md-3">
            <a 
                href="{{ route('payments.my.report', array_merge(request()->all(), ['pdf' => 1])) }}" 
                target="_blank" 
                class="btn btn-danger w-100">
                📄 Gerar PDF
            </a>
        </div>

    </form>

    {{-- ============================= --}}
    {{-- INFO DO PERÍODO --}}
    {{-- ============================= --}}
    <div class="mb-3">
        <small class="text-muted">
            Exibindo dados de: 
            <strong>
                {{ \Carbon\Carbon::parse(request('month', now()))->translatedFormat('F/Y') }}
            </strong>
        </small>
    </div>

    {{-- ============================= --}}
    {{-- DATATABLE --}}
    {{-- ============================= --}}
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
