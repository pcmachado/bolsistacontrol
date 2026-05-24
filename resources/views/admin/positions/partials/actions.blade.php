<div class="d-flex justify-content-center gap-2">
    {{-- Botão Visualizar --}}
    <a href="{{ route('admin.positions.show', $id) }}" 
       class="btn btn-sm btn-info" 
       title="Visualizar">
        <i class="bi bi-eye"></i>
    </a>
    {{-- Botão Editar --}}
    <a href="{{ route('admin.positions.edit', $id) }}" 
       class="btn btn-sm btn-primary" 
       title="Editar">
        <i class="bi bi-pencil-square"></i>
    </a>

    {{-- Botão Excluir --}}
    <form action="{{ route('admin.positions.destroy', $id) }}" method="POST" 
          onsubmit="return confirm('Tem certeza que deseja excluir?');" 
          style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
            <i class="bi bi-trash"></i>
        </button>
    </form>
</div>