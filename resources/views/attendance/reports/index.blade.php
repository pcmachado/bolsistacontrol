@extends('layouts.app')

@section('title', 'Relatórios Mensais')

@section('content')
<div class="container-fluid py-3">
    <h2 class="mb-4">Relatórios Mensais</h2>

    @if($submissions->isEmpty())
        <div class="alert alert-info">
            Nenhuma submissão disponível para relatório.
        </div>
    @else
        <div class="card shadow-sm">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Projeto</th>
                        <th>Mês/Ano</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $submission)
                        <tr>
                            <td>{{ $submission->project?->name ?? '-' }}</td>
                            <td>{{ str_pad($submission->month, 2, '0', STR_PAD_LEFT) }}/{{ $submission->year }}</td>
                            <td>
                                <span class="badge bg-{{ $submission->status_color }}">
                                    {{ $submission->status_label }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('attendance.reports.monthly', $submission) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    Visualizar
                                </a>

                                <a href="{{ route('attendance.reports.monthly', [$submission, 'pdf' => 1]) }}"
                                   class="btn btn-sm btn-outline-danger"
                                   target="_blank">
                                    PDF
                                </a>

                                <a href="{{ route('attendance.reports.monthly.blank', [$submission, 'blank' => 1]) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   target="_blank">
                                    Em branco
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
