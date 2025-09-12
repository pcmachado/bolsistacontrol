<div class="flex space-x-2">
    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800">Editar</a>
    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-600 hover:text-red-800">Excluir</button>
    </form>
</div>