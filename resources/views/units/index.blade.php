<!-- resources/views/units/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Unidades</h4>
        <a href="{{ route('admin.units.create') }}" class="btn btn-primary">Adicionar Nova Unidade</a>
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
                        <th>Cidade</th>
                        <th>Endereço</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($unidades as $unidade)
                    <tr>
                        <td>{{ $unidade->nome }}</td>
                        <td>{{ $unidade->cidade }}</td>
                        <td>{{ $unidade->endereco }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.units.edit', $unidade) }}" class="btn btn-sm btn-outline-info">Editar</a>
                            <form action="{{ route('admin.units.destroy', $unidade) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta unidade?');">
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
        {{ $unidades->links() }}
    </div>
</div>
@endsection