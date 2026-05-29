<div class="d-flex justify-content-center gap-2">
    {{-- Botão Visualizar --}}
    <a href="{{ route('admin.users.show', $user->id) }}" 
       class="btn btn-sm btn-info" 
       title="Visualizar">
        <i class="bi bi-eye"></i>
    </a>
    {{-- Botão Editar --}}
    @can('update', $user)
    <a href="{{ route('admin.users.edit', $user->id) }}" 
       class="btn btn-sm btn-primary" 
       title="Editar">
        <i class="bi bi-pencil-square"></i>
    </a>

    @if(! $user->scholarshipHolder)
        <a href="{{ route('admin.scholarship_holders.create', ['user_id' => $user->id]) }}"
           class="btn btn-sm btn-success"
           title="Adicionar bolsista">
            <i class="bi bi-person-plus"></i>
        </a>
    @endif
    @endcan

    {{-- Botão Excluir --}}
    @can('delete', $user)
    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
          onsubmit="return confirm('Tem certeza que deseja excluir?');" 
          style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
            <i class="bi bi-trash"></i>
        </button>
    </form>
    @endcan
</div>
