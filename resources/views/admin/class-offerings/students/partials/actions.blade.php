<form method="POST" action="{{ route('admin.class.students.remove', ['class' => $classId, 'student' => $student->id]) }}" onsubmit="return confirm('Remover aluno da turma?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-outline-danger">Remover</button>
</form>
