@extends('layouts.app')

@section('title', 'Meus Pagamentos')

@section('content')
<div class="container-fluid">

    <h1 class="mb-4">Meus Pagamentos</h1>

    {{-- NOVO: Abas de projeto --}}
    @include('attendance.partials.project-tabs')

    @php
        $selectedMonth = request('month', now()->format('Y-m'));
    @endphp

    <x-month-navigation
        route="payments.my"
        :month="$selectedMonth"
        :params="['project_id' => $activeProjectId]"
    />

    {{-- ============================= --}}
    {{-- AÇÕES --}}
    {{-- ============================= --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('payments.my', ['project_id' => $activeProjectId]) }}" class="btn btn-outline-secondary">
            Limpar
        </a>

        <a
            href="{{ route('payments.my.report', array_merge(request()->all(), ['month' => $selectedMonth, 'pdf' => 1, 'project_id' => $activeProjectId])) }}"
            target="_blank"
            class="btn btn-danger">
            📄 Gerar PDF
        </a>
    </div>

    {{-- ============================= --}}
    {{-- INFO DO PERÍODO --}}
    {{-- ============================= --}}
    <div class="mb-3">
        <small class="text-muted">
            Exibindo dados de: 
            <strong>
                {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->translatedFormat('F/Y') }}
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
