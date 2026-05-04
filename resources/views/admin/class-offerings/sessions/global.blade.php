@extends('layouts.app')

@section('title', 'Relatório Global de Turmas')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="bi bi-file-earmark-bar-graph me-2"></i>
            Relatório Global de Turmas
        </h2>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.academic-reports.class-sessions.global') }}"
           class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-clockwise me-1"></i> Atualizar
        </a>

        <a href="{{ route('admin.academic-reports.class-sessions.pdf') }}"
           class="btn btn-danger me-2">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>

        <a href="{{ route('admin.academic-reports.class-sessions.excel') }}"
           class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Excel
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Disciplina</th>
                        <th>Turma</th>
                        <th>Data da Aula</th>
                        <th>Professor</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($sessions as $session)
                    <tr>
                        <td>{{ $session->discipline?->name }}</td>
                        <td>{{ $session->classOffering?->name }}</td>
                        <td>{{ $session->date->format('d/m/Y') }}</td>
                        <td>{{ $session->professor?->user?->name }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection