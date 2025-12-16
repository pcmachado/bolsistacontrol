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

                {{-- Curso --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                    @error('course_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

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
                    <label class="form-label fw-semibold">Projeto (opcional)</label>
                    <select name="project_id" class="form-select @error('project_id') is-invalid @enderror">
                        <option value="">Nenhum</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Nome da Turma --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome da Turma (opcional)</label>
                    <input type="text" name="name" class="form-control" placeholder="Ex: Turma A Noturno">
                </div>

                <div class="row">
                    {{-- Semestre --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Semestre</label>
                        <input type="text" name="semester" class="form-control" placeholder="Ex: 2025/1">
                    </div>

                    {{-- Ano --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Ano</label>
                        <input type="number" name="year" class="form-control" placeholder="Ex: 2025">
                    </div>

                    {{-- Capacidade --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Capacidade</label>
                        <input type="number" name="capacity" class="form-control" placeholder="Ex: 30">
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
