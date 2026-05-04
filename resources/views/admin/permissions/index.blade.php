@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Ações/Permissões do Sistema</h1>

    <a href="{{ route('admin.permissions.create') }}" class="btn btn-success mb-3">
        <i class="bi bi-plus-circle"></i> Nova Permissão
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Criada em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td>{{ $permission->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Tem certeza que deseja excluir?')">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">Nenhuma permissão encontrada.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
