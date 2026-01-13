<a href="{{ route('admin.supervisors.edit', $row->id) }}"
   class="btn btn-sm btn-warning">
   <i class="bi bi-pencil"></i>
</a>

<form action="{{ route('admin.supervisors.destroy', $row->id) }}"
      method="POST" class="d-inline">
    @csrf @method('DELETE')
    <button class="btn btn-sm btn-danger"
            onclick="return confirm('Excluir vínculo?')">
        <i class="bi bi-trash"></i>
    </button>
</form>
