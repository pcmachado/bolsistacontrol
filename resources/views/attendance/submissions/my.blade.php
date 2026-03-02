@extends('layouts.app')

@section('title', 'Envio Mensal de Frequência')

@section('content')
<div class="container">
    <h3 class="mb-4">
        <i class="bi bi-calendar-check me-2"></i>
        Envio Mensal de Frequência
    </h3>

    @forelse ($submissions as $submission)
        <div class="card mb-3 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <strong>
                        {{ str_pad($submission->month, 2, '0', STR_PAD_LEFT) }}/{{ $submission->year }}
                    </strong>
                    <div class="text-muted small">
                        {{ $submission->records_count }} registros ·
                        {{ $submission->total_hours }} horas
                    </div>
                </div>

                <div>
                    <span class="badge bg-{{ $submission->status_color }}">
                        {{ ucfirst($submission->status) }}
                    </span>

                    <a href="{{ route('attendance.submissions.show', $submission) }}"
                       class="btn btn-sm btn-outline-primary ms-2">
                        Ver
                    </a>

                    @can('submit', $submission)
                        <form action="{{ route('attendance.submissions.submit', $submission) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success ms-1">
                                Enviar mês
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            Nenhum registro encontrado.
        </div>
    @endforelse
</div>
@endsection
