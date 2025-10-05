@extends('layouts.app')

@section('content')
<h1>Permissões</h1>

<a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">Nova Permissão</a>

<table class="table mt-3">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($permissions as $permission)
        <tr>
            <td>{{ $permission->name }}</td>
            <td>
                <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir permissão?')">Excluir</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection