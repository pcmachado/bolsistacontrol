@extends('layouts.app')

@section('title', 'Versões do Sistema')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark">Versões do Sistema (Release Notes)</h1>

        <div class="d-flex gap-2">
            <form action="{{ route('admin.system_releases.import-git') }}" method="POST" onsubmit="return confirm('Importar ou atualizar a versão atual com base no Git?');">
                @csrf
                <button type="submit" class="btn btn-outline-primary shadow-sm" @disabled(! $gitAvailable)>
                    <i class="bi bi-arrow-repeat me-1"></i> Importar do Git
                </button>
            </form>

            <a href="{{ route('admin.system_releases.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Nova Versão
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="alert alert-info border-0 shadow-sm">
        <div class="fw-semibold mb-1">Controle de versão da aplicação</div>
        <div class="small mb-0">
            A versão atual do sistema é <strong>{{ $currentVersion ?? 'dev' }}</strong>
            <span class="text-muted">(origem: {{ $currentVersionSource ?? 'local' }})</span>.
            Quando o Git está disponível, o botão lê o histórico e gera as notas automaticamente.
            Em produção Docker sem repositório Git, use <code>version.txt</code> na raiz da aplicação ou <code>APP_VERSION</code> no ambiente e cadastre as notas pela opção manual.
            O modal aparece uma única vez por usuário e depois continua acessível pelo link do rodapé.
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Versão (Tag)</th>
                        <th>Data de Lançamento</th>
                        <th>Origem</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($releases as $release)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">{{ $release->version }}</td>
                            <td>{{ ($release->released_at ?? $release->created_at)?->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($release->is_automatic)
                                    <span class="badge bg-info-subtle text-info-emphasis border">Git</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border">Manual</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.system_releases.edit', $release) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.system_releases.destroy', $release) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir esta versão?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Nenhuma versão cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
