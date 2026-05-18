@extends('layouts.app')

@section('title', 'Editar Curso')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i> Editar Curso</h2>
            <p class="text-muted mb-0">Atualize os dados do curso e altere o projeto vinculado quando necessário.</p>
        </div>

        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary shadow-sm px-3">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

<form method="POST" action="{{ route('admin.courses.update', $course->id) }}">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Nome:</strong>
                <input type="text" name="name" placeholder="Nome" class="form-control" value="{{ old('name', $course->name) }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 mt-3">
            <div class="form-group">
                <strong>Capacidade de Alunos:</strong>
                <input
                    type="number"
                    name="capacity"
                    placeholder="Ex: 30"
                    class="form-control"
                    value="{{ old('capacity', $course->capacity) }}"
                    min="1"
                >
            </div>
        </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i> Erros encontrados:</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.courses.update', $course) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Nome do Curso</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $course->name) }}"
                            class="form-control @error('name') is-invalid @enderror"
                            required
                        >
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="project_id" class="form-label fw-semibold">Projeto</label>
                        <select
                            name="project_id"
                            id="project_id"
                            class="form-select @error('project_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Selecione o projeto</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}"
                                    @selected(old('project_id', $course->projects->first()?->id) == $project->id)
                                >
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="institution_id" class="form-label fw-semibold">Instituição</label>
                        <select
                            name="institution_id"
                            id="institution_id"
                            class="form-select @error('institution_id') is-invalid @enderror"
                        >
                            <option value="">Nenhuma</option>
                            @foreach($institutions as $institution)
                                <option value="{{ $institution->id }}"
                                    @selected(old('institution_id', $course->institution_id) == $institution->id)
                                >
                                    {{ $institution->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('institution_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="duration_hours" class="form-label fw-semibold">Carga Horária (horas)</label>
                        <input
                            type="number"
                            name="duration_hours"
                            id="duration_hours"
                            value="{{ old('duration_hours', $course->duration_hours) }}"
                            class="form-control @error('duration_hours') is-invalid @enderror"
                            min="0"
                        >
                        @error('duration_hours')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="start_date" class="form-label fw-semibold">Data de Início</label>
                        <input
                            type="date"
                            name="start_date"
                            id="start_date"
                            value="{{ old('start_date', optional($course->start_date)->format('Y-m-d')) }}"
                            class="form-control @error('start_date') is-invalid @enderror"
                        >
                        @error('start_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label fw-semibold">Data de Término</label>
                        <input
                            type="date"
                            name="end_date"
                            id="end_date"
                            value="{{ old('end_date', optional($course->end_date)->format('Y-m-d')) }}"
                            class="form-control @error('end_date') is-invalid @enderror"
                        >
                        @error('end_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold">Descrição do Curso</label>
                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            class="form-control tox-tinymce @error('description') is-invalid @enderror"
                        >{{ old('description', $course->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="prerequisites" class="form-label fw-semibold">Pré-requisitos</label>
                        <textarea
                            name="prerequisites"
                            id="prerequisites"
                            rows="3"
                            class="form-control @error('prerequisites') is-invalid @enderror"
                        >{{ old('prerequisites', $course->prerequisites) }}</textarea>
                        @error('prerequisites')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <input type="hidden" name="active" value="0">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="active"
                                value="1"
                                id="active"
                                @checked(old('active', $course->active))
                            >
                            <label class="form-check-label" for="active">
                                Curso ativo
                            </label>
                        </div>
                        @error('active')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-2"></i> Salvar alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('components.tinymce')
@endpush