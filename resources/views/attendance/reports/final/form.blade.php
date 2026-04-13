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
                @can('submit', $report)
                    <form method="POST"
                          action="{{ route('final-reports.submit', $report) }}">
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
