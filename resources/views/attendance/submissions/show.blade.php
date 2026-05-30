@extends('layouts.app')

@section('title', 'Submissão Mensal de Frequência')

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
                Submissão mensal - {{ str_pad($submission->month, 2, '0', STR_PAD_LEFT) }}/{{ $submission->year }}
            </h2>

            <div class="text-muted">Bolsista: <strong>{{ $submission->scholarshipHolder->user->name }}</strong></div>
            <div class="text-muted">Projeto: <strong>{{ $submission->project?->name ?? '-' }}</strong></div>
        </div>

        <div>
            <span class="badge bg-{{ $submission->status_color }} fs-6">
                {{ $submission->status_label }}
            </span>
        </div>
    </div>

    @if ($submission->status === 'draft')
        <div class="alert alert-info">
            <strong>Rascunho:</strong> revise os registros abaixo antes de enviar para homologação.
        </div>
    @elseif ($submission->status === 'submitted')
        <div class="alert alert-warning">
            <strong>Aguardando homologação.</strong> Nenhuma alteração pode ser feita neste momento.
        </div>
    @elseif ($submission->status === 'approved')
        <div class="alert alert-success">
            <strong>Submissão homologada.</strong> Este mês está encerrado.
        </div>
    @elseif ($submission->status === 'rejected')
        <div class="alert alert-danger">
            <strong>Submissão rejeitada.</strong> Os registros foram devolvidos para correção.
        </div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-header fw-bold">Registros de Frequência</div>

        <div class="card-body p-0">
            @if ($submission->attendanceRecords->isEmpty())
                <div class="p-3 text-muted">Nenhum registro vinculado a esta submissão.</div>
            @else
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%">Data</th>
                            <th style="width: 25%">Projeto</th>
                            <th style="width: 15%">Horas</th>
                            <th>Descrição</th>
                            @if ($submission->status === 'draft')
                                <th style="width: 10%">Ação</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($submission->attendanceRecords as $record)
                            <tr>
                                <td>{{ $record->date->format('d/m/Y') }}</td>
                                <td>{{ $record->project?->name ?? '-' }}</td>
                                <td>{{ number_format($record->hours, 2) }}</td>
                                <td>{{ $record->description ?: '-' }}</td>

                                @if ($submission->status === 'draft')
                                    <td class="text-center">
                                        <form method="POST"
                                              action="{{ route($removeRecordRoute, [$submission, $record]) }}"
                                              onsubmit="return confirm('Remover este registro da submissão?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-outline-danger" title="O registro volta para o diário">
                                                Remover do mês
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
                <button class="btn btn-success">Enviar para Homologação</button>
            </form>
        @endcan

        @can('approve', $submission)
            <form method="POST" action="{{ route($approveRoute, $submission) }}">
                @csrf
                <button class="btn btn-primary">Aprovar</button>
            </form>

            <form method="POST" action="{{ route($rejectRoute, $submission) }}" class="d-flex gap-2">
                @csrf
                <input type="text" name="reason" class="form-control" placeholder="Motivo da rejeição" required>
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
