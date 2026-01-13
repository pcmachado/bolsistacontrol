<form action="{{ route('admin.class-offerings.scholarship_holders.destroy', [$offering->id, $student->id]) }}"
      method="POST" class="d-inline">
    @csrf
    @method('DELETE')

    <button class="btn btn-sm btn-danger"
            onclick="return confirm('Remover bolsista da turma?')">
        <i class="bi bi-trash"></i>
    </button>
</form>
