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
            <p class="mb-1">
                Esta pagina centraliza os manuais didaticos por perfil, preparados para leitura em tela e revisao antes da geracao em PDF.
            </p>
            <p class="text-muted mb-0">
                Nos documentos, as marcacoes <code>[PRINT ...]</code> indicam onde inserir capturas de tela na versao final.
            </p>
        </div>
    </div>

    @php
        $manualDocs = $manualDocs ?? [
            'guia-executivo' => 'GUIA-EXECUTIVO.md',
            'readme' => 'README.md',
            'bolsista' => 'perfis/bolsista.md',
            'coordenacao' => 'perfis/coordenacao.md',
            'coordenador-adjunto' => 'perfis/coordenador-adjunto.md',
            'coordenador-adjunto-geral' => 'perfis/coordenador-adjunto-geral.md',
            'coordenador-geral' => 'perfis/coordenador-geral.md',
            'professor' => 'perfis/professor.md',
            'orientador' => 'perfis/orientador.md',
            'admin' => 'perfis/admin.md',
            'professor-supervisor' => 'perfis/professor-supervisor.md',
            'turmas-alunos' => 'perfis/turmas-alunos.md',
        ];

        $manualTitles = [
            'guia-executivo' => 'Guia Executivo',
            'readme' => 'Indice dos Manuais',
            'bolsista' => 'Bolsista',
            'coordenacao' => 'Coordenacao',
            'coordenador-adjunto' => 'Coordenador Adjunto',
            'coordenador-adjunto-geral' => 'Coordenador Adjunto Geral',
            'coordenador-geral' => 'Coordenador Geral',
            'professor' => 'Professor',
            'orientador' => 'Orientador',
            'admin' => 'Administrador',
            'professor-supervisor' => 'Professor/Supervisor',
            'turmas-alunos' => 'Turmas e Alunos',
        ];

        $manualCards = [
            'bolsista' => ['title' => 'Bolsista', 'description' => 'Frequencia, submissao mensal, relatorios e pagamentos.'],
            'coordenador-adjunto' => ['title' => 'Coordenador Adjunto', 'description' => 'Homologacao e acompanhamento no escopo vinculado.'],
            'coordenador-adjunto-geral' => ['title' => 'Coordenador Adjunto Geral', 'description' => 'Acompanhamento institucional, pendencias, relatorios e financeiro.'],
            'coordenador-geral' => ['title' => 'Coordenador Geral', 'description' => 'Gestao institucional, cadastros, homologacoes, financeiro e permissoes.'],
            'professor' => ['title' => 'Professor', 'description' => 'Dashboard, minhas turmas, lancamentos e fechamento mensal.'],
            'orientador' => ['title' => 'Orientador', 'description' => 'Acompanhamento de bolsistas, analise de frequencias e orientacoes.'],
            'admin' => ['title' => 'Administrador', 'description' => 'Cadastros estruturais, governanca e operacoes criticas.'],
            'turmas-alunos' => ['title' => 'Turmas e Alunos', 'description' => 'Lancamentos academicos, controle mensal e envio ao financeiro.'],
        ];

        $selectedDoc = $selectedDoc ?? 'guia-executivo';
        $manualPath = base_path('docs/manual/' . ($manualDocs[$selectedDoc] ?? 'GUIA-EXECUTIVO.md'));
        $manualHtml = file_exists($manualPath)
            ? \Illuminate\Support\Str::markdown(file_get_contents($manualPath))
            : '<p class="text-muted mb-0">Guia executivo nao encontrado.</p>';
    @endphp

    <div class="row g-3">
        @foreach($manualCards as $docKey => $card)
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $card['title'] }}</h5>
                        <p class="card-text text-muted flex-grow-1">{{ $card['description'] }}</p>
                        <a href="{{ route('manual.index', ['doc' => $docKey]) }}" class="btn btn-sm {{ $selectedDoc === $docKey ? 'btn-primary' : 'btn-outline-primary' }}">
                            Abrir guia
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white fw-semibold d-flex flex-column flex-lg-row justify-content-between gap-2">
            <span><i class="bi bi-file-earmark-text me-2"></i> Documentos do Manual</span>
            <span class="text-muted small">Sugestao: use a impressao do navegador para gerar PDF apos inserir os prints finais.</span>
        </div>
        <div class="card-body border-bottom">
            <div class="d-flex flex-wrap gap-2">
                @foreach($manualDocs as $docKey => $path)
                    <a href="{{ route('manual.index', ['doc' => $docKey]) }}" class="btn btn-sm {{ $selectedDoc === $docKey ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ $manualTitles[$docKey] ?? \Illuminate\Support\Str::headline($docKey) }}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="card-body manual-html-content">
            {!! $manualHtml !!}
        </div>
    </div>

</div>
@endsection
