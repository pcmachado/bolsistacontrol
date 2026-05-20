@extends('layouts.app')

@section('title', 'Versões do Sistema')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark">Versões do Sistema (Release Notes)</h1>
        <a href="{{ route('admin.system_releases.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Nova Versão
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Versão (Tag)</th>
                        <th>Data de Lançamento</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($releases as $release)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">{{ $release->version }}</td>
                            <td>{{ $release->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.system_releases.edit', $release) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.system_releases.destroy', $release) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir esta versão?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Nenhuma versão cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection