@extends('layouts.app')

@section('title', 'Editar Turma')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-pencil me-2"></i> Editar Turma</h2>

        <div>
            <a href="{{ route('admin.class-offerings.index') }}" class="btn btn-outline-secondary px-3 me-2">
                <i class="bi bi-arrow-left me-2"></i> Voltar
            </a>

            {{-- Botão para gerenciar disciplinas da turma --}}
            <a href="{{ route('admin.class-offerings.disciplines.index', $offering->id) }}"
               class="btn btn-info px-3">
                <i class="bi bi-diagram-3 me-2"></i> Disciplinas da Turma
            </a>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <h5 class="fw-bold"><i class="bi bi-exclamation-triangle me-2"></i> Atenção</h5>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-info-circle me-2"></i> Informações da Turma
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('admin.class-offerings.update', $offering->id) }}">
                @csrf
                @method('PUT')

                {{-- Unidade --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Unidade</label>
                    <select name="unit_id" class="form-select" required>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}"
                                {{ $offering->unit_id == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Projeto --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Projeto</label>
                    <select id="project_id" name="project_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" 
                                {{ old('project_id', $offering->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Curso --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Curso</label>
                    <select id="course_id" name="course_id" class="form-select" required></select>
                </div>

                {{-- Nome --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome da Turma</label>
                    <input type="text" name="name" class="form-control" 
                           value="{{ $offering->name }}" placeholder="Ex: Turma A Noturno">
                </div>

                {{-- Linha de campos --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Semestre</label>
                        <input type="text" name="semester" class="form-control"
                               value="{{ $offering->semester }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Ano</label>
                        <input type="number" name="year" class="form-control"
                               value="{{ $offering->year }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Capacidade</label>
                        <input type="number" name="capacity" class="form-control"
                               value="{{ $offering->capacity }}">
                    </div>
                </div>

                {{-- Datas --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Data de Início</label>
                        <input type="date" name="start_date" class="form-control"
                               value="{{ $offering->start_date }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Data de Conclusão</label>
                        <input type="date" name="end_date" class="form-control"
                               value="{{ $offering->end_date }}">
                    </div>
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['planned' => 'Planejado', 'ongoing' => 'Em andamento', 'finished' => 'Concluído', 'cancelled' => 'Cancelado'] as $value => $label)
                            <option value="{{ $value }}" {{ $offering->status === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check2-circle me-2"></i> Atualizar Turma
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const projectCourses = @json($projectCourses);
    const projectSelect = document.getElementById('project_id');
    const courseSelect = document.getElementById('course_id');
    const selectedCourse = @json(old('course_id', $offering->course_id));

    function reloadCourseOptions() {
        const projectId = projectSelect.value;
        const courses = projectCourses[projectId] || [];

        courseSelect.innerHTML = '';

        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = courses.length
            ? 'Selecione...'
            : 'Nenhum curso ativo vinculado ao projeto';
        courseSelect.appendChild(placeholder);

        for (const course of courses) {
            const option = document.createElement('option');
            option.value = String(course.id);
            option.textContent = course.name;
            if (selectedCourse && String(course.id) === String(selectedCourse)) {
                option.selected = true;
            }
            courseSelect.appendChild(option);
        }
    }

    projectSelect.addEventListener('change', reloadCourseOptions);
    reloadCourseOptions();
</script>
@endpush
