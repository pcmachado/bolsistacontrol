@extends('layouts.app')

@section('title', 'Frequências')

@section('content')
<div class="container-fluid">
    <h1 class="mb-3">Registros de Frequência</h1>

    @include('attendance.partials.project-tabs')

    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-1">
                <strong>Carga Horária Mensal</strong>
                <span>
                    {{ number_format($total, 1) }}h /
                    {{ number_format($limit, 1) }}h
                </span>
            </div>

            @php
                $percent = $limit > 0 ? min(100, ($total / $limit) * 100) : 0;
            @endphp

            <div class="progress" style="height: 10px;">
                <div class="progress-bar {{ $total > $limit ? 'bg-danger' : 'bg-success' }}"
                    style="width: {{ $percent }}%">
                </div>
            </div>
        </div>
    </div>

    @if($submission && $submission->status === 'rejected')
        <div class="alert alert-danger">
            <strong>Rejeitado:</strong> {{ $submission->rejected_reason }}
        </div>
    @endif

    @if($isClosed)
        <div class="alert alert-warning">
            Este mês já foi enviado ou homologado para o projeto selecionado.
        </div>
    @endif

    @php
        $current = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $prev = $current->copy()->subMonth()->format('Y-m');
        $next = $current->copy()->addMonth()->format('Y-m');
        $currentMonthBoundary = now()->startOfMonth()->format('Y-m');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ $prev < $oldestMonth ? '#' : route('attendance.index', ['project_id' => $activeProjectId, 'month' => $prev, 'status' => request('status')]) }}"
           class="btn btn-outline-secondary {{ $prev < $oldestMonth ? 'disabled' : '' }}">
            &larr;
        </a>

        <h4 class="mb-0">
            {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F/Y') }}
        </h4>

        <a href="{{ $next > $currentMonthBoundary ? '#' : route('attendance.index', ['project_id' => $activeProjectId, 'month' => $next, 'status' => request('status')]) }}"
           class="btn btn-outline-secondary {{ $next > $currentMonthBoundary ? 'disabled' : '' }}">
            &rarr;
        </a>
    </div>

    <div class="d-flex gap-2 my-3">
        @if($activeProjectId && (! $submission || in_array($submission->status, ['draft', 'rejected'])))
            <a href="{{ route('attendance.create', ['project_id' => $activeProjectId, 'month' => $month]) }}" class="btn btn-primary">
                Registrar frequência
            </a>
        @endif

        @if($submission && $submission->status === 'draft')
            <a href="{{ route('attendance.submissions.show', $submission) }}" class="btn btn-success">
                Enviar mês para homologação
            </a>
        @endif
    </div>

    <form method="GET" class="row g-2 mb-3">
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="project_id" value="{{ $activeProjectId }}">

        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Todos</option>
                <option value="draft" @selected(request('status') === 'draft')>Em edição</option>
                <option value="submitted" @selected(request('status') === 'submitted')>Enviados</option>
                <option value="approved" @selected(request('status') === 'approved')>Homologados</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejeitados</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    {!! $dataTable->table() !!}
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
