<a href="{{ route('admin.class-offerings.edit', $row->id) }}"
   class="btn btn-sm btn-warning">
    <i class="bi bi-pencil"></i>
</a>

<a href="{{ route('admin.class.students.list', $row->id) }}"
   class="btn btn-sm btn-primary">
    <i class="bi bi-people"></i>
</a>

<a href="{{ route('admin.class-offerings.disciplines', $row->id) }}"
   class="btn btn-sm btn-info">
    <i class="bi bi-diagram-3"></i>
</a>