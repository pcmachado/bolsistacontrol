<a href="{{ route('admin.teachers.edit', $t->id) }}"
   class="btn btn-sm btn-warning">
    <i class="bi bi-pencil"></i>
</a>

<form action="{{ route('admin.teachers.destroy', $t->id) }}"
      method="POST" class="d-inline">
    @csrf @method('DELETE')
    <button class="btn btn-sm btn-danger"
            onclick="return confirm('Excluir professor?')">
        <i class="bi bi-trash"></i>
    </button>
</form>
