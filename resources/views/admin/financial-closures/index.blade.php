@extends('layouts.app')

@section('title', 'Fechamento Financeiro')

@section('content')
<div class="container-fluid">

    <h1 class="mb-4">Fechamento Financeiro</h1>

    @php
        $selectedMonth = request('month', $monthString);
    @endphp

    <x-month-navigation
        route="admin.financial-closures.index"
        :month="$selectedMonth"
        :params="request()->except('month')"
    />

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('admin.financial-closures.preview', ['month' => $selectedMonth]) }}"
           class="btn btn-outline-secondary">
            👁 Prévia
        </a>
    </div>

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