@extends('layouts.app')

@section('title', 'Relatório Consolidado Mensal')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
        <h2 class="mb-0">Relatório Consolidado Mensal</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('attendance.reports.monthly-consolidated', ['month' => $monthInput, 'pdf' => 1]) }}" target="_blank" class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i> Gerar PDF
            </a>
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <x-month-navigation
                route="attendance.reports.monthly-consolidated"
                :month="$monthInput"
                :min-month="$minMonth"
                button-class="btn btn-sm btn-outline-secondary"
                title-class="h6 fw-semibold mb-0"
                class="d-flex align-items-center gap-2 flex-wrap"
            />
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <strong>Período: {{ $period->translatedFormat('F/Y') }}</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Projeto</th>
                            <th class="text-end">Horas previstas</th>
                            <th class="text-end">Horas registradas</th>
                            <th class="text-end">Diferença</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td>{{ $row['project_name'] }}</td>
                                <td class="text-end">{{ number_format($row['expected_hours'], 1, ',', '.') }}h</td>
                                <td class="text-end">{{ number_format($row['registered_hours'], 1, ',', '.') }}h</td>
                                <td class="text-end {{ $row['difference_hours'] < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($row['difference_hours'], 1, ',', '.') }}h
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">Sem projetos vinculados.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td>Total geral</td>
                            <td class="text-end">{{ number_format($totals['expected_hours'], 1, ',', '.') }}h</td>
                            <td class="text-end">{{ number_format($totals['registered_hours'], 1, ',', '.') }}h</td>
                            <td class="text-end {{ $totals['difference_hours'] < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($totals['difference_hours'], 1, ',', '.') }}h</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
