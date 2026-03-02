@extends('layouts.app')

@section('title', 'Frequências')

@section('content')
<div class="container-fluid">
    <h1 class="mb-3">Registros de Frequência</h1>

    <h2 class="mb-3">
        Frequência – {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F/Y') }}
    </h2>

    @if($submission)
        <span class="badge bg-{{ $submission->status === 'draft' ? 'secondary' : ($submission->status === 'submitted' ? 'info' : ($submission->status === 'approved' ? 'success' : 'danger')) }}">
            {{ ucfirst($submission->status) }}
        </span>
    @else
        <span class="badge bg-warning">Mês em edição</span>
    @endif

    <div class="d-flex gap-2 my-3">
        @if(! $submission || in_array($submission->status, ['draft','rejected']))
            <a href="{{ route('attendance.create') }}" class="btn btn-primary">
                ➕ Registrar frequência
            </a>
        @endif

        @if($submission && $submission->status === 'draft')
            <a href="{{ route('attendance.submissions.show', $submission) }}"
            class="btn btn-success">
                📤 Enviar mês para homologação
            </a>
        @endif
    </div>

    {{-- Filtros --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="month"
                   name="month"
                   value="{{ request('month') }}"
                   class="form-control">
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Todos</option>
                <option value="draft" @selected(request('status') === 'draft')>
                    Rascunhos
                </option>
                <option value="submitted" @selected(request('status') === 'submitted')>
                    Enviados
                </option>
                <option value="rejected" @selected(request('status') === 'rejected')>
                    Rejeitados
                </option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    {{-- DataTable --}}
    {!! $dataTable->table() !!}
</div>
@endsection
@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
