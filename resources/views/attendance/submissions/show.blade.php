@extends('layouts.app')

@section('title', 'Submissão Mensal de Frequência')

@section('content')
<div class="container-fluid py-3">

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">
                Submissão mensal — {{ str_pad($submission->month, 2, '0', STR_PAD_LEFT) }}/{{ $submission->year }}
            </h2>

            <div class="text-muted">
                Bolsista:
                <strong>{{ $submission->scholarshipHolder->user->name }}</strong>
            </div>
        </div>

        {{-- Status --}}
        <div>
            @php
                $statusColors = [
                    'draft'     => 'secondary',
                    'submitted' => 'warning',
                    'approved'  => 'success',
                    'rejected'  => 'danger',
                ];
            @endphp

            <span class="badge bg-{{ $statusColors[$submission->status] ?? 'info' }} fs-6">
                {{ ucfirst($submission->status) }}
            </span>
        </div>
    </div>

    {{-- Avisos de estado --}}
    @if ($submission->status === 'draft')
        <div class="alert alert-info">
            <strong>Rascunho:</strong>
            revise os registros abaixo antes de enviar para homologação.
        </div>
    @elseif ($submission->status === 'submitted')
        <div class="alert alert-warning">
            <strong>Aguardando homologação.</strong>
            Nenhuma alteração pode ser feita neste momento.
        </div>
    @elseif ($submission->status === 'approved')
        <div class="alert alert-success">
            <strong>Submissão homologada.</strong>
            Este mês está encerrado.
        </div>
    @elseif ($submission->status === 'rejected')
        <div class="alert alert-danger">
            <strong>Submissão rejeitada.</strong>
            Os registros foram devolvidos para correção.
        </div>
    @endif

    {{-- Registros --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header fw-bold">
            Registros de Frequência
        </div>

        <div class="card-body p-0">
            @if ($submission->records->isEmpty())
                <div class="p-3 text-muted">
                    Nenhum registro vinculado a esta submissão.
                </div>
            @else
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%">Data</th>
                            <th style="width: 15%">Horas</th>
                            <th>Descrição</th>

                            {{-- ação só em draft --}}
                            @if ($submission->status === 'draft')
                                <th style="width: 10%">Ação</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($submission->records as $record)
                            <tr>
                                <td>{{ $record->date->format('d/m/Y') }}</td>
                                <td>{{ number_format($record->hours, 2) }}</td>
                                <td>{{ $record->description ?: '—' }}</td>

                                @if ($submission->status === 'draft')
                                    <td class="text-center">
                                        <form method="POST"
                                              action="{{ route('attendance.submissions.records.remove', [$submission, $record]) }}"
                                              onsubmit="return confirm('Remover este registro da submissão?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-outline-danger"
                                                title="O registro volta para o diário">
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

    {{-- Ações --}}
    <div class="d-flex gap-2">

        {{-- Enviar (bolsista) --}}
        @can('submit', $submission)
            <form method="POST"
                  action="{{ route('attendance.submissions.submit', $submission) }}">
                @csrf
                <button class="btn btn-success">
                    Enviar para Homologação
                </button>
            </form>
        @endcan

        {{-- Aprovar / Rejeitar (coordenação) --}}
        @can('approve', $submission)
            <form method="POST"
                  action="{{ route('attendance.submissions.approve', $submission) }}">
                @csrf
                <button class="btn btn-primary">
                    Aprovar
                </button>
            </form>

            <button class="btn btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#rejectModal">
                Rejeitar
            </button>
        @endcan

        <a href="{{ route('attendance.submissions.index') }}"
           class="btn btn-outline-secondary ms-auto">
            Voltar
        </a>
    </div>

</div>

{{-- Modal de rejeição --}}
@include('attendance.submissions.partials.reject-modal')

@endsection
