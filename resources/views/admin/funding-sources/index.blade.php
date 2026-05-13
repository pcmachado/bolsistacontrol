@extends('layouts.app')

@section('title', 'Formas de Fomento')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Formas de Fomento</h1>

        <a href="{{ route('admin.funding-sources.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nova Forma de Fomento
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.funding-sources.index') }}" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Nome</label>
            <input
                type="text"
                name="name"
                value="{{ request('name') }}"
                class="form-control"
                placeholder="Pesquisar por nome...">
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>

        <div class="col-md-2">
            <a href="{{ route('admin.funding-sources.index') }}" class="btn btn-outline-secondary w-100">
                Limpar
            </a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th class="text-end">Valor Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($fundingSources as $source)
                        <tr>
                            <td>
                                <strong>{{ $source->name }}</strong>
                                @if($source->code)
                                    <div class="small text-muted">{{ $source->code }}</div>
                                @endif
                            </td>
                            <td>{{ $source->type === 'internal' ? 'Interna' : 'Externa' }}</td>
                            <td class="text-end">R$ {{ number_format($source->total_amount ?? 0, 2, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge {{ $source->active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $source->active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.funding-sources.show', $source) }}" class="btn btn-sm btn-outline-secondary">
                                    Ver
                                </a>
                                <a href="{{ route('admin.funding-sources.edit', $source) }}" class="btn btn-sm btn-primary">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Nenhuma forma de fomento encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($fundingSources->hasPages())
            <div class="card-footer">
                {{ $fundingSources->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
