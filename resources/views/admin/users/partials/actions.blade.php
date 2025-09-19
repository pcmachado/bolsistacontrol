<div class="flex items-center justify-center space-x-4">
    <a href="{{ route('admin.users.edit', $id) }}" class="text-indigo-600 hover:text-indigo-900">
        <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('admin.users.destroy', $id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-600 hover:text-red-900">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>