@extends('layouts.project-wizard')

@section('title', 'Cursos do Projeto')

@section('wizard-content')

<h4 class="mb-4 fw-bold">Vincular Cursos</h4>

<form method="POST"
      action="{{ route('admin.projects.store.step3', $project) }}">
    @csrf

    <p class="text-muted mb-3">
        Selecione os cursos vinculados ao projeto e informe o período acadêmico.
    </p>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:50px;"></th>
                    <th>Curso</th>
                    <th style="width:120px;">Semestre</th>
                    <th style="width:120px;">Ano</th>
                    <th style="width:80px;">Ativo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                    @php
                        $pivot = $project->courses->firstWhere('id', $course->id)?->pivot;
                    @endphp
                    <tr>
                        <td>
                            <input type="checkbox"
                                   name="courses[{{ $course->id }}][selected]"
                                   value="1"
                                   class="form-check-input course-check"
                                   data-id="{{ $course->id }}"
                                   {{ old("courses.{$course->id}.selected", $pivot ? '1' : null) ? 'checked' : '' }}>
                        </td>

                        <td>{{ $course->name }}</td>

                        <td>
                            <input type="text"
                                   name="courses[{{ $course->id }}][semester]"
                                   class="form-control form-control-sm semester-input"
                                   placeholder="Ex: 1º"
                                   value="{{ old("courses.{$course->id}.semester", $pivot->semester ?? '') }}"
                                   {{ old("courses.{$course->id}.selected", $pivot ? '1' : null) ? '' : 'disabled' }}>
                        </td>

                        <td>
                            <input type="number"
                                   name="courses[{{ $course->id }}][year]"
                                   class="form-control form-control-sm year-input"
                                   placeholder="2025"
                                   value="{{ old("courses.{$course->id}.year", $pivot->year ?? date('Y')) }}"
                                   {{ old("courses.{$course->id}.selected", $pivot ? '1' : null) ? '' : 'disabled' }}>
                        </td>

                        <td class="text-center">
                            <input type="hidden"
                                   name="courses[{{ $course->id }}][active]"
                                   value="0"
                                   class="active-hidden"
                                   {{ old("courses.{$course->id}.selected", $pivot ? '1' : null) ? '' : 'disabled' }}>
                            <input type="checkbox"
                                   name="courses[{{ $course->id }}][active]"
                                   value="1"
                                   class="form-check-input active-input"
                                   {{ old("courses.{$course->id}.active", $pivot?->active ? '1' : null) ? 'checked' : '' }}
                                   {{ old("courses.{$course->id}.selected", $pivot ? '1' : null) ? '' : 'disabled' }}>
                        </td>

                        <input type="hidden"
                               name="courses[{{ $course->id }}][course_id]"
                               value="{{ $course->id }}">
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Ações --}}
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('admin.projects.create.step2', $project) }}"
           class="btn btn-outline-secondary">
            ← Voltar
        </a>

        <button type="submit" class="btn btn-primary">
            Próximo →
        </button>
    </div>

</form>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.course-check').forEach(cb => {
    cb.addEventListener('change', function () {
        const row = this.closest('tr');

        row.querySelectorAll('.semester-input, .year-input, .active-input, .active-hidden')
           .forEach(input => {
               input.disabled = !this.checked;
           });

        if (this.checked) {
            row.querySelector('.active-input').checked = true;
        }

        if (!this.checked) {
            row.querySelectorAll('input').forEach(i => {
                if (i.type !== 'hidden' && i.type !== 'checkbox') {
                    i.value = '';
                }
                if (i.classList.contains('active-input')) {
                    i.checked = false;
                }
            });
        }
    });
});
</script>
@endpush
