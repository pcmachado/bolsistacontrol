@extends('layouts.app')

@section('title', 'Frequências')

@section('content')
<div class="container-fluid">

    <h1 class="mb-3 text-black">Registros de Frequência</h1>

    {{-- ============================= --}}
    {{-- RESUMO DE HORAS --}}
    {{-- ============================= --}}
    <div class="card mb-3 shadow-sm text-body">
        <div class="card-body">

            <div class="d-flex justify-content-between mb-1">
                <strong>Carga Horária Mensal</strong>
                <span>
                    {{ number_format($total,1) }}h /
                    {{ number_format($limit,1) }}h
                </span>
            </div>

            @php
                $percent = $limit > 0 ? min(100, ($total / $limit) * 100) : 0;
            @endphp

            <div class="progress" style="height: 10px;">
                <div class="progress-bar 
                    {{ $total > $limit ? 'bg-danger' : 'bg-success' }}"
                    style="width: {{ $percent }}%">
                </div>
            </div>

        </div>
    </div>

    {{-- ============================= --}}
    {{-- ALERTAS DE STATUS --}}
    {{-- ============================= --}}
    @if($submission)

        @if($submission->status === 'rejected')
            <div class="alert alert-danger">
                <strong>Submissão rejeitada:</strong><br>
                {{ $submission->rejected_reason }}

                @if($submission->rejected_at)
                    <div class="mt-2">
                        <small>
                            Prazo para correção até:
                            <strong>
                                {{ $submission->rejected_at->copy()->addDays(7)->format('d/m/Y') }}
                            </strong>
                        </small>
                    </div>
                @endif
            </div>
        @endif

        @if($submission->status === 'submitted')
             <div class="alert alert-warning">
                <strong>Submissão enviada:</strong><br>
                📤 Submissão enviada. Aguardando homologação. Nenhuma alteração é permitida neste momento.
            </div>
        @endif

        @if($submission->status === 'approved')
             <div class="alert alert-success">
                <strong>Submissão homologada:</strong><br>
                ✅ Submissão homologada. Este mês está encerrado. Nenhuma alteração é permitida.
            </div>
        @endif

    @endif

    @if($isClosed)
        <div class="alert alert-danger">
            🔒 Período fechado. Alterações não permitidas.
        </div>
    @endif

    {{-- ============================= --}}
    {{-- NAVEGAÇÃO DE MÊS --}}
    {{-- ============================= --}}
    @php
        $current = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $currentMonth = now()->startOfMonth();

        $prev = $current->copy()->subMonth()->format('Y-m');
        $next = $current->copy()->addMonth()->format('Y-m');

        $disablePrev = $prev < $oldestMonth;
        $disableNext = $next > $currentMonth;
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">

        <a href="{{ $disablePrev ? '#' : route('attendance.index', ['month' => $prev, 'status' => request('status')]) }}"
           class="btn btn-outline-secondary {{ $disablePrev ? 'disabled' : '' }}">
            ←
        </a>

        <h4 class="mb-0 text-black">
            {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F/Y') }}
        </h4>

        <a href="{{ $disableNext ? '#' : route('attendance.index', ['month' => $next, 'status' => request('status')]) }}"
           class="btn btn-outline-secondary {{ $disableNext ? 'disabled' : '' }}">
            →
        </a>

    </div>

    {{-- ============================= --}}
    {{-- AÇÕES --}}
    {{-- ============================= --}}
    <div class="d-flex flex-wrap gap-2 my-3">

        @php
            $canEdit = !$isClosed && (!$submission || in_array($submission->status, ['draft','rejected']));
        @endphp

        @if($canEdit)
            <a href="{{ route('attendance.create') }}" class="btn btn-primary">
                ➕ Registrar frequência
            </a>
        @else
            <button class="btn btn-secondary" disabled>
                🔒 Registro bloqueado
            </button>
        @endif

        @if($submission && $submission->status === 'draft')
            <a href="{{ route('my-attendance.submissions.show', $submission) }}"
               class="btn btn-success">
                📤 Enviar mês para homologação
            </a>
        @endif

    </div>

    {{-- ============================= --}}
    {{-- FILTROS --}}
    {{-- ============================= --}}
    <form method="GET" class="row g-2 mb-3">

        <input type="hidden" name="month" value="{{ $month }}">

        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Todos</option>

                <option value="draft" @selected(request('status') === 'draft')>
                    Em edição
                </option>

                <option value="submitted" @selected(request('status') === 'submitted')>
                    Enviados
                </option>

                <option value="approved" @selected(request('status') === 'approved')>
                    Homologados
                </option>

                <option value="rejected" @selected(request('status') === 'rejected')>
                    Rejeitados
                </option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>

    </form>

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