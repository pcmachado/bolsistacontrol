@extends('layouts.app')

@section('title', 'Manual do Sistema')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row align-items-start align-items-md-center gap-2 mb-4">
        <h1 class="fw-bold mb-0">
            <i class="bi bi-journal-bookmark me-2"></i> Manual do ProBolsas
        </h1>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p class="mb-0">
                Esta pagina centraliza o acesso aos guias por perfil.
                Voce pode evoluir o conteudo por contexto operacional no futuro.
            </p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Bolsista</h5>
                    <p class="card-text text-muted">Rotina de frequencia, submissao mensal e pagamentos.</p>
                    <a href="{{ route('manual.index', ['doc' => 'bolsista']) }}" class="btn btn-sm btn-outline-primary">
                        Abrir guia
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Coordenacao</h5>
                    <p class="card-text text-muted">Homologacao, filtros operacionais e acompanhamento.</p>
                    <a href="{{ route('manual.index', ['doc' => 'coordenacao']) }}" class="btn btn-sm btn-outline-primary">
                        Abrir guia
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Administrador</h5>
                    <p class="card-text text-muted">Cadastros estruturais, governanca e operacoes criticas.</p>
                    <a href="{{ route('manual.index', ['doc' => 'admin']) }}" class="btn btn-sm btn-outline-primary">
                        Abrir guia
                    </a>
                </div>
            </div>
        </div>
    </div>

    @php
        $manualDocs = $manualDocs ?? [
            'guia-executivo' => 'GUIA-EXECUTIVO.md',
            'readme' => 'README.md',
            'bolsista' => 'perfis/bolsista.md',
            'coordenacao' => 'perfis/coordenacao.md',
            'admin' => 'perfis/admin.md',
            'professor-supervisor' => 'perfis/professor-supervisor.md',
        ];
        $selectedDoc = $selectedDoc ?? 'guia-executivo';
        $manualPath = base_path('docs/manual/' . ($manualDocs[$selectedDoc] ?? 'GUIA-EXECUTIVO.md'));
        $manualHtml = file_exists($manualPath)
            ? \Illuminate\Support\Str::markdown(file_get_contents($manualPath))
            : '<p class="text-muted mb-0">Guia executivo nao encontrado.</p>';
    @endphp

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-file-earmark-text me-2"></i> Documentos do Manual
        </div>
        <div class="card-body border-bottom">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('manual.index', ['doc' => 'guia-executivo']) }}" class="btn btn-sm {{ $selectedDoc === 'guia-executivo' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Guia Executivo
                </a>
                <a href="{{ route('manual.index', ['doc' => 'readme']) }}" class="btn btn-sm {{ $selectedDoc === 'readme' ? 'btn-primary' : 'btn-outline-primary' }}">
                    README
                </a>
                <a href="{{ route('manual.index', ['doc' => 'bolsista']) }}" class="btn btn-sm {{ $selectedDoc === 'bolsista' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Bolsista
                </a>
                <a href="{{ route('manual.index', ['doc' => 'coordenacao']) }}" class="btn btn-sm {{ $selectedDoc === 'coordenacao' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Coordenacao
                </a>
                <a href="{{ route('manual.index', ['doc' => 'admin']) }}" class="btn btn-sm {{ $selectedDoc === 'admin' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Administrador
                </a>
                <a href="{{ route('manual.index', ['doc' => 'professor-supervisor']) }}" class="btn btn-sm {{ $selectedDoc === 'professor-supervisor' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Professor/Supervisor
                </a>
            </div>
        </div>
        <div class="card-body manual-html-content">
            {!! $manualHtml !!}
        </div>
    </div>

</div>
@endsection
