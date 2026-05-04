@extends('layouts.app')

@section('title', 'Fechamento Financeiro')

@section('content')
<div class="container-fluid">

    <h1 class="mb-4">Fechamento Financeiro</h1>

    {{-- Filtros --}}
    <form method="GET" class="row g-2 mb-4 align-items-end">

        <div class="col-md-3">
            <label class="form-label">Competência</label>
            <input type="month"
                   name="month"
                   value="{{ request('month', $monthString) }}"
                   class="form-control">
        </div>

        <div class="col-md-3">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>

        <div class="col-md-3">
            <a href="{{ route('admin.financial-closures.preview', ['month' => request('month', $monthString)]) }}"
               class="btn btn-outline-secondary w-100">
                👁 Prévia
            </a>
        </div>

    </form>

    {{-- Tabela --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Unidade</th>
                        <th>Competência</th>
                        <th>Fechado em</th>
                        <th>Responsável</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>

                @forelse($closures as $closure)
                    <tr>
                        <td>{{ $closure->unit?->name }}</td>
                        <td>{{ str_pad($closure->month, 2, '0', STR_PAD_LEFT) }}/{{ $closure->year }}</td>
                        <td>{{ $closure->closed_at?->format('d/m/Y H:i') }}</td>
                        <td>{{ $closure->closedBy?->name }}</td>
                        <td>
                            <form method="POST"
                                  action="{{ route('admin.financial-closures.destroy', $closure) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    Reabrir
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Nenhum fechamento encontrado.
                        </td>
                    </tr>
                @endforelse

                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection