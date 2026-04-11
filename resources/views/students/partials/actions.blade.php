{{-- resources/views/students/partials/actions.blade.php --}}

<div class="btn-group btn-group-sm">

    <a href="{{ route('students.edit', $s) }}"
       class="btn btn-outline-warning">
        <i class="bi bi-pencil"></i>
    </a>

    <form method="POST"
          action="{{ route('students.destroy', $s) }}"
          onsubmit="return confirm('Excluir aluno?')">
        @csrf
        @method('DELETE')

        <button class="btn btn-outline-danger">
            <i class="bi bi-trash"></i>
        </button>
    </form>

</div>