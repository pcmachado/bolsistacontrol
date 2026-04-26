@extends('layouts.app')

@section('title', 'Relatório Final')

@section('content')
<div class="container py-4">

    <h2 class="mb-3">Relatório Final de Atividades</h2>

    <p>
        <strong>Bolsista:</strong>
        {{ $report->scholarshipHolder->user->name }}
    </p>

    <p>
        <strong>Status:</strong>
        <span class="badge bg-info">
            {{ ucfirst($report->status) }}
        </span>
    </p>

    <hr>

    <h5>Atividades desenvolvidas</h5>
    <p>{{ nl2br(e($report->activities)) }}</p>

    <h5>Resultados alcançados</h5>
    <p>{{ nl2br(e($report->results)) }}</p>

    <h5>Contribuições para o projeto</h5>
    <p>{{ nl2br(e($report->contributions)) }}</p>

    <div class="d-flex gap-2 mt-4">
        <a href="{{ route('attendance.reports.final.pdf', $report) }}"
           class="btn btn-danger">
            📄 Baixar PDF
        </a>

        <a href="{{ route('dashboard') }}"
           class="btn btn-outline-secondary ms-auto">
            Voltar
        </a>
    </div>

</div>
@endsection
