<a href="{{ route('admin.disciplines.edit', $row->id) }}"
   class="btn btn-sm btn-warning">
    <i class="bi bi-pencil-square"></i>
</a>

<form action="{{ route('admin.disciplines.destroy', $row->id) }}"
      method="POST"
      class="d-inline">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger"
            onclick="return confirm('Confirmar exclusão?')">
        <i class="bi bi-trash"></i>
    </button>
</form>
