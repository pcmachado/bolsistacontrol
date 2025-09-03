<!-- resources/views/scholarship-holders/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Bolsistas</h4>
        <a href="{{ route('bolsistas.create') }}" class="btn btn-primary">Adicionar Novo Bolsista</a>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Cargo</th>
                        <th>Unidade</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bolsistas as $bolsista)
                    <tr>
                        <td>{{ $bolsista->nome }}</td>
                        <td>{{ $bolsista->email }}</td>
                        <td>{{ $bolsista->cargo->nome ?? 'N/A' }}</td>
                        <td>
                            @foreach($bolsista->unidades as $unidade)
                                {{ $unidade->nome }}@if(!$loop->last), @endif
                            @endforeach
                        </td>
                        <td class="text-end">
                            <a href="{{ route('bolsistas.edit', $bolsista) }}" class="btn btn-sm btn-outline-info">Editar</a>
                            <form action="{{ route('bolsistas.destroy', $bolsista) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este bolsista?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $bolsistas->links() }}
    </div>
</div>
@endsection