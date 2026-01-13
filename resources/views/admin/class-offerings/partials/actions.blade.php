<a href="{{ route('admin.class-offerings.edit', $row->id) }}"
   class="btn btn-sm btn-warning">
    <i class="bi bi-pencil"></i>
</a>

{{-- Futuro: gerenciar disciplinas --}}
<a href="{{ route('admin.class-offerings.disciplines', $row->id) }}"
   class="btn btn-sm btn-info">
    <i class="bi bi-diagram-3"></i>
</a>

<form action="{{ route('admin.class-offerings.destroy', $row->id) }}"
      method="POST" class="d-inline">
    @csrf @method('DELETE')
    <button onclick="return confirm('Remover turma?')"
            class="btn btn-sm btn-danger">
        <i class="bi bi-trash"></i>
    </button>
</form>
