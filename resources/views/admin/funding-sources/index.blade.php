@extends('layouts.app')

@section('title', 'Fonte de Recursos')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Fonte de Recursos</h1>

    {{-- ============================= --}}
    {{-- FILTROS --}}
    {{-- ============================= --}}
    <form method="GET" class="row g-2 mb-3 align-items-end">

        <div class="col-md-3">
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
            <a href="{{ route('funding-sources.index') }}" class="btn btn-outline-secondary w-100">
                Limpar
            </a>
        </div>

        <div class="col-md-3">
            <a 
                href="{{ route('funding-sources.report', array_merge(request()->all(), ['pdf' => 1])) }}" 
                target="_blank" 
                class="btn btn-danger w-100">
                📄 Gerar PDF
            </a>
        </div>

    </form>

    {{-- ============================= --}}
    {{-- DATATABLE --}}
    {{-- ============================= --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($fundingSources as $source)
                    <tr>
                        <td>{{ $source->name }}</td>
                        <td class="text-end">
                            <a href="{{ route('funding-sources.edit', $source) }}" class="btn btn-sm btn-primary">
                                Editar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted">
                            Nenhuma fonte de recurso encontrada.
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