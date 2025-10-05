<div class="d-flex justify-content-center gap-2">
    {{-- Botão Visualizar --}}
    <a href="{{ route('admin.projects.show', $id) }}" 
       class="btn btn-sm btn-info rounded-0" 
       title="Visualizar">
        <i class="bi bi-eye"></i>
    </a>
    {{-- Botão Editar --}}
    <a href="{{ route('admin.projects.edit', $id) }}" 
       class="btn btn-sm btn-primary rounded-0" 
       title="Editar">
        <i class="bi bi-pencil-square"></i>
    </a>

    {{-- Botão Excluir --}}
    <form action="{{ route('admin.projects.destroy', $id) }}" method="POST" 
          onsubmit="return confirm('Tem certeza que deseja excluir?');" 
          style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger rounded-0" title="Excluir">
            <i class="bi bi-trash"></i>
        </button>
    </form>
</div>