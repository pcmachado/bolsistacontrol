@extends('layouts.app')

@section('title', 'Relatório Final de Atividades')

@section('content')
<div class="container-fluid py-4">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h2 class="mb-3">Relatório Final de Atividades</h2>

            <p class="text-muted">
                Este formulário deve ser preenchido ao final da vigência da bolsa,
                consolidando as atividades desenvolvidas no projeto.
            </p>

            <div class="alert alert-info">
                As informações registradas serão utilizadas para comprovação institucional,
                prestação de contas e avaliação final.
            </div>
        </div>
    </div>

    @if($report)
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <h5 class="mb-3">Relatório já cadastrado</h5>

                <p>
                    <strong>Status:</strong>

                    @php
                        $statusColors = [
                            'draft' => 'secondary',
                            'submitted' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                        ];
                    @endphp

                    <span class="badge bg-{{ $statusColors[$report->status] ?? 'info' }}">
                        {{ ucfirst($report->status) }}
                    </span>
                </p>

                <div class="d-flex flex-wrap gap-2 mt-3">

                    <a href="{{ route('attendance.reports.final.edit', $report) }}"
                       class="btn btn-primary">
                        ✏ Editar Relatório
                    </a>

                    <a href="{{ route('attendance.reports.final.show', $report) }}"
                       class="btn btn-outline-info">
                        👁 Visualizar
                    </a>

                    <a href="{{ route('attendance.reports.final.pdf', $report) }}"
                       class="btn btn-danger">
                        📄 PDF
                    </a>

                </div>

            </div>
        </div>
    @else
        <div class="card shadow-sm border-0 bg-light">
            <div class="card-body text-center py-5">
                
                <!-- Ícone ilustrativo opcional -->
                <div class="mb-3">
                    <i class="bi bi-file-earmark-x display-4 text-secondary opacity-50"></i>
                </div>

                <h5 class="mb-2 fw-bold text-dark">Nenhum relatório cadastrado</h5>
                <p class="text-muted mb-4 small">
                    Você ainda não possui relatórios. Escolha uma das opções abaixo para começar.
                </p>

                <!-- Container Flexbox para alinhar os botões lado a lado (e empilhar no celular) -->
                <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-2">

                    <!-- Botão Principal (Ação Padrão) -->
                    <a href="{{ route('attendance.reports.final.create') }}"
                    class="btn btn-primary shadow-sm px-4">
                    <i class="bi bi-plus-lg me-1"></i> Criar Relatório
                    </a>

                    <!-- Botão Secundário (Formulário POST intacto e seguro) -->
                    <form method="POST" action="{{ route('attendance.reports.final.blank') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary shadow-sm px-4">
                            <i class="bi bi-file-earmark-text me-1"></i> Gerar em Branco
                        </button>
                    </form>

                </div>

            </div>
        </div>
    @endif

</div>
@endsection