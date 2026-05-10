@extends('layouts.app')

@section('title', 'SubmissÃ£o Mensal de FrequÃªncia')

@section('content')
@php
    $isSelfRoute = request()->routeIs('my-attendance.submissions.*');
    $showIndexRoute = $isSelfRoute ? 'my-attendance.submissions.my' : 'attendance.submissions.index';
    $submitRoute = $isSelfRoute ? 'my-attendance.submissions.submit' : 'attendance.submissions.submit';
    $approveRoute = $isSelfRoute ? 'my-attendance.submissions.approve' : 'attendance.submissions.approve';
    $rejectRoute = $isSelfRoute ? 'my-attendance.submissions.reject' : 'attendance.submissions.reject';
    $removeRecordRoute = $isSelfRoute ? 'my-attendance.submissions.records.remove' : 'attendance.submissions.records.remove';
@endphp

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">
                SubmissÃ£o mensal â€” {{ str_pad($submission->month, 2, '0', STR_PAD_LEFT) }}/{{ $submission->year }}
            </h2>

            <div class="text-muted">Bolsista: <strong>{{ $submission->scholarshipHolder->user->name }}</strong></div>
            <div class="text-muted">Projeto: <strong>{{ $submission->project?->name ?? '-' }}</strong></div>
        </div>

        <div>
            @php
                $statusColors = [
                    'draft' => 'secondary',
                    'submitted' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                ];
            @endphp

            <span class="badge bg-{{ $statusColors[$submission->status] ?? 'info' }} fs-6">
                {{ ucfirst($submission->status) }}
            </span>
        </div>
    </div>

    @if ($submission->status === 'draft')
        <div class="alert alert-info">
            <strong>Rascunho:</strong> revise os registros abaixo antes de enviar para homologaÃ§Ã£o.
        </div>
    @elseif ($submission->status === 'submitted')
        <div class="alert alert-warning">
            <strong>Aguardando homologaÃ§Ã£o.</strong> Nenhuma alteraÃ§Ã£o pode ser feita neste momento.
        </div>
    @elseif ($submission->status === 'approved')
        <div class="alert alert-success">
            <strong>SubmissÃ£o homologada.</strong> Este mÃªs estÃ¡ encerrado.
        </div>
    @elseif ($submission->status === 'rejected')
        <div class="alert alert-danger">
            <strong>SubmissÃ£o rejeitada.</strong> Os registros foram devolvidos para correÃ§Ã£o.
        </div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-header fw-bold">Registros de FrequÃªncia</div>

        <div class="card-body p-0">
            @if ($submission->attendanceRecords->isEmpty())
                <div class="p-3 text-muted">Nenhum registro vinculado a esta submissÃ£o.</div>
            @else
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%">Data</th>
                            <th style="width: 25%">Projeto</th>
                            <th style="width: 15%">Horas</th>
                            <th>DescriÃ§Ã£o</th>
                            @if ($submission->status === 'draft')
                                <th style="width: 10%">AÃ§Ã£o</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($submission->attendanceRecords as $record)
                            <tr>
                                <td>{{ $record->date->format('d/m/Y') }}</td>
                                <td>{{ $record->project?->name ?? '-' }}</td>
                                <td>{{ number_format($record->hours, 2) }}</td>
                                <td>{{ $record->description ?: 'â€”' }}</td>

                                @if ($submission->status === 'draft')
                                    <td class="text-center">
                                        <form method="POST"
                                              action="{{ route($removeRecordRoute, [$submission, $record]) }}"
                                              onsubmit="return confirm('Remover este registro da submissÃ£o?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-outline-danger" title="O registro volta para o diÃ¡rio">
                                                Remover do mÃªs
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="d-flex gap-2">
        @can('submit', $submission)
            <form method="POST" action="{{ route($submitRoute, $submission) }}">
                @csrf
                <button class="btn btn-success">Enviar para HomologaÃ§Ã£o</button>
            </form>
        @endcan

        @can('approve', $submission)
            <form method="POST" action="{{ route($approveRoute, $submission) }}">
                @csrf
                <button class="btn btn-primary">Aprovar</button>
            </form>

            <form method="POST" action="{{ route($rejectRoute, $submission) }}" class="d-flex gap-2">
                @csrf
                <input type="text" name="reason" class="form-control" placeholder="Motivo da rejeiÃ§Ã£o" required>
                <button class="btn btn-danger">Rejeitar</button>
            </form>
        @endcan

        <a href="{{ route($showIndexRoute, ['project_id' => $submission->project_id, 'month' => sprintf('%04d-%02d', $submission->year, $submission->month)]) }}"
           class="btn btn-outline-secondary ms-auto">
            Voltar
        </a>
    </div>
</div>
@endsection
