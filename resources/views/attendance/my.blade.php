@extends('layouts.app')

@section('title', 'FrequÃªncias')

@section('content')
<div class="container-fluid">
    <h1 class="mb-3 text-black">Registros de FrequÃªncia</h1>

    @include('attendance.partials.project-tabs')

    <div class="card mb-3 shadow-sm text-body">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-1">
                <strong>Carga HorÃ¡ria Mensal</strong>
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

    @if($submission)
        @if($submission->status === 'rejected')
            <div class="alert alert-danger">
                <strong>SubmissÃ£o rejeitada:</strong><br>
                {{ $submission->rejected_reason }}

                @if($submission->rejected_at)
                    <div class="mt-2">
                        <small>
                            Prazo para correÃ§Ã£o atÃ©:
                            <strong>{{ $submission->rejected_at->copy()->addDays(7)->format('d/m/Y') }}</strong>
                        </small>
                    </div>
                @endif
            </div>
        @endif

        @if($submission->status === 'submitted')
            <div class="alert alert-warning">
                <strong>SubmissÃ£o enviada:</strong><br>
                ðŸ“¤ SubmissÃ£o enviada. Aguardando homologaÃ§Ã£o. Nenhuma alteraÃ§Ã£o Ã© permitida neste momento.
            </div>
        @endif

        @if($submission->status === 'approved')
            <div class="alert alert-success">
                <strong>SubmissÃ£o homologada:</strong><br>
                âœ… SubmissÃ£o homologada. Este mÃªs estÃ¡ encerrado. Nenhuma alteraÃ§Ã£o Ã© permitida.
            </div>
        @endif
    @endif

    @if($isClosed)
        <div class="alert alert-danger">
            ðŸ”’ PerÃ­odo fechado. AlteraÃ§Ãµes nÃ£o permitidas para este projeto.
        </div>
    @endif

    @php
        $current = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $currentMonth = now()->startOfMonth();
        $prev = $current->copy()->subMonth()->format('Y-m');
        $next = $current->copy()->addMonth()->format('Y-m');
        $disablePrev = $prev < $oldestMonth;
        $disableNext = $next > $currentMonth->format('Y-m');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ $disablePrev ? '#' : route('attendance.my', ['project_id' => $activeProjectId, 'month' => $prev, 'status' => request('status')]) }}"
           class="btn btn-outline-secondary {{ $disablePrev ? 'disabled' : '' }}">
            â†
        </a>

        <h4 class="mb-0 text-black">
            {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F/Y') }}
        </h4>

        <a href="{{ $disableNext ? '#' : route('attendance.my', ['project_id' => $activeProjectId, 'month' => $next, 'status' => request('status')]) }}"
           class="btn btn-outline-secondary {{ $disableNext ? 'disabled' : '' }}">
            â†’
        </a>
    </div>

    <div class="d-flex flex-wrap gap-2 my-3">
        @php
            $canEdit = $activeProjectId && ! $isClosed && (! $submission || in_array($submission->status, ['draft', 'rejected']));
        @endphp

        @if($canEdit)
            <a href="{{ route('attendance.create', ['project_id' => $activeProjectId, 'month' => $month]) }}" class="btn btn-primary">
                âž• Registrar frequÃªncia
            </a>
        @else
            <button class="btn btn-secondary" disabled>
                ðŸ”’ Registro bloqueado
            </button>
        @endif

        @if($submission && $submission->status === 'draft')
            <a href="{{ route('my-attendance.submissions.show', $submission) }}" class="btn btn-success">
                ðŸ“¤ Enviar mÃªs para homologaÃ§Ã£o
            </a>
        @endif
    </div>

    <form method="GET" class="row g-2 mb-3">
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="project_id" value="{{ $activeProjectId }}">

        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Todos</option>
                <option value="draft" @selected(request('status') === 'draft')>Em ediÃ§Ã£o</option>
                <option value="submitted" @selected(request('status') === 'submitted')>Enviados</option>
                <option value="approved" @selected(request('status') === 'approved')>Homologados</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejeitados</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

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
