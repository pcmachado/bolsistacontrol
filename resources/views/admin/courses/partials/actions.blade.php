<a href="{{ route('admin.courses.edit', $course->id) }}"
   class="btn btn-sm btn-warning" title="Editar Curso">
    <i class="bi bi-pencil"></i>
</a>

<a href="{{ route('admin.courses.class-offerings.index', $course) }}"
   class="btn btn-sm btn-info"
   title="Ver Turmas do Curso">
    <i class="bi bi-collection"></i>
</a>

<a href="{{ route('admin.courses.disciplines.index', $course) }}"
   class="btn btn-sm btn-primary" title="Gerenciar Disciplinas do Curso">
    <i class="bi bi-journal-text"></i>
</a>

<form action="{{ route('admin.courses.destroy', $course->id) }}"
      method="POST"
      class="d-inline">
    @csrf @method('DELETE')
    <button class="btn btn-sm btn-danger"
            onclick="return confirm('Deseja excluir este curso?')">
        <i class="bi bi-trash"></i>
    </button>
</form>
