<form action="{{ route('admin.class-offerings.sessions.destroy', $session->id) }}"
      method="POST" class="d-inline">
    @csrf @method('DELETE')

    <button class="btn btn-sm btn-danger"
            onclick="return confirm('Excluir aula?')">
        <i class="bi bi-trash"></i>
    </button>
</form>
