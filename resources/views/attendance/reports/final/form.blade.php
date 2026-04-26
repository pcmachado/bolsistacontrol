@extends('layouts.app')

@section('title', 'Relatório Final de Atividades')

@section('content')
<div class="container-fluid py-3">

    <h2 class="mb-3">
        Relatório Final de Atividades
    </h2>

    <div class="alert alert-info">
        Este relatório deve ser preenchido ao final da vigência do edital.
    </div>

    <form method="POST"
          action="{{ isset($report)
                ? route('attendance.reports.final.update', $report)
                : route('attendance.reports.final.store') }}">

        @csrf
        @isset($report)
            @method('PUT')
        @endisset

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">Dados do vínculo</h5>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Bolsista:</strong>
                        {{ auth()->user()->name }}
                    </div>

                    <div class="col-md-6">
                        <strong>Projeto:</strong>
                        {{ $project->name ?? '-' }}
                    </div>

                    <div class="col-md-6 mt-2">
                        <strong>Edital / Portaria:</strong>
                        {{ $pivot->edital_portaria ?? '-' }}
                    </div>

                    <div class="col-md-6 mt-2">
                        <strong>Data inicial:</strong>
                        {{ \Carbon\Carbon::parse($pivot->start_date)->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Data final</label>
            @php
                $endDate = $report->end_date ?? $pivot->end_date ?? null;
            @endphp

            <input type="date"
                name="end_date"
                class="form-control"
                value="{{ old('end_date', $endDate ? \Carbon\Carbon::parse($endDate)->format('Y-m-d') : '') }}">
        </div>

        {{-- Atividades desenvolvidas --}}
        <div class="mb-3">
            <label class="form-label fw-bold">
                Atividades desenvolvidas
            </label>
            <textarea name="activities"
                      rows="6"
                      class="form-control"
                      required>{{ old('activities', $report->activities ?? '') }}</textarea>
        </div>

        {{-- Resultados alcançados --}}
        <div class="mb-3">
            <label class="form-label fw-bold">
                Resultados alcançados
            </label>
            <textarea name="results"
                      rows="5"
                      class="form-control"
                      required>{{ old('results', $report->results ?? '') }}</textarea>
        </div>

        {{-- Contribuições para o projeto --}}
        <div class="mb-3">
            <label class="form-label fw-bold">
                Contribuições para o projeto
            </label>
            <textarea name="contributions"
                      rows="5"
                      class="form-control"
                      required>{{ old('contributions', $report->contributions ?? '') }}</textarea>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary">
                💾 Salvar
            </button>

            @isset($report)
                <a href="{{ route('attendance.reports.final.pdf', $report) }}"
                class="btn btn-danger">
                    📄 Visualizar PDF
                </a>

                <a href="{{ route('attendance.reports.final.blank', $report) }}"
                class="btn btn-outline-secondary">
                    📝 Em Branco
                </a>
            @endisset

            @isset($report)
                @can('submit', $report)
                    <form method="POST"
                          action="{{ route('attendance.reports.final.submit', $report) }}">
                        @csrf
                        <button class="btn btn-success">
                            📤 Enviar para aprovação
                        </button>
                    </form>
                @endcan
            @endisset

            <a href="{{ route('dashboard') }}"
               class="btn btn-outline-secondary ms-auto">
                Voltar
            </a>
        </div>

    </form>
</div>
@endsection
