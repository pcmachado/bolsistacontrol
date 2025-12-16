<a href="{{ route('admin.courses.edit', $course->id) }}"
   class="btn btn-sm btn-warning">
    <i class="bi bi-pencil"></i>
</a>

<a href="{{ route('admin.class-offerings.index') }}?filter_course={{ $course->id }}"
   class="btn btn-sm btn-info">
    <i class="bi bi-collection"></i>
</a>

<a href="{{ route('admin.disciplines.index') }}?filter_course={{ $course->id }}"
   class="btn btn-sm btn-primary">
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
