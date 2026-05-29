@extends('layouts.app')

@section('title', 'Criar Turma')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="bi bi-collection me-2"></i> Criar Nova Turma</h2>

        <a href="{{ route('admin.class-offerings.index') }}" class="btn btn-outline-secondary px-3">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <h5 class="fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Erros encontrados</h5>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-info-circle me-2"></i> Informações da Turma
        </div>

        <div class="card-body">

            <form action="{{ route('admin.class-offerings.store') }}" method="POST">
                @csrf

                {{-- Unidade --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Unidade</label>
                    <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                    @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Projeto --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Projeto</label>
                    <select id="project_id" name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Curso --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Curso</label>
                    <select id="course_id" name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                        <option value="">Selecione um projeto primeiro...</option>
                    </select>
                    @error('course_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Nome da Turma --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome da Turma (opcional)</label>
                    <input type="text" name="name" class="form-control" placeholder="Ex: Turma A Noturno">
                </div>

                <div class="row">
                    {{-- Semestre --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Semestre</label>
                        <input type="text" name="semester" class="form-control" placeholder="Ex: 2025/1" value="{{ old('semester') }}">
                    </div>

                    {{-- Ano --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Ano</label>
                        <input type="number" name="year" class="form-control" placeholder="Ex: 2025" value="{{ old('year') }}">
                    </div>

                    {{-- Capacidade --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Capacidade</label>
                        <input type="number" id="capacity" name="capacity" class="form-control" placeholder="Ex: 30" value="{{ old('capacity') }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Horas aula por dia</label>
                        <input
                            type="number"
                            name="hours_per_day"
                            class="form-control @error('hours_per_day') is-invalid @enderror"
                            placeholder="Ex: 4"
                            min="0.25"
                            max="24"
                            step="0.25"
                            value="{{ old('hours_per_day') }}"
                        >
                        @error('hours_per_day') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Datas --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Data de Início</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Data de Conclusão</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="planned">Planejado</option>
                        <option value="ongoing">Em andamento</option>
                        <option value="finished">Concluído</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>

                <div class="text-end mt-4">
                    <button class="btn btn-primary px-4">
                        <i class="bi bi-check2-circle me-2"></i> Criar Turma
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
    const selectedCourse = @json(old('course_id'));
    const capacityInput = document.getElementById('capacity');

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

        fillCapacityFromSelectedCourse();
    }

    function fillCapacityFromSelectedCourse() {
        if (capacityInput.value) {
            return;
        }

        const projectId = projectSelect.value;
        const selected = (projectCourses[projectId] || [])
            .find(course => String(course.id) === String(courseSelect.value));

        if (selected?.capacity) {
            capacityInput.value = selected.capacity;
        }
    }

    projectSelect.addEventListener('change', reloadCourseOptions);
    courseSelect.addEventListener('change', fillCapacityFromSelectedCourse);
    reloadCourseOptions();
</script>
@endpush
