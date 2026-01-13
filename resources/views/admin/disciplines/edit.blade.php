@extends('layouts.app')

@section('title', 'Editar Disciplina')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-pencil-square me-2"></i> Editar Disciplina
        </h2>

        <a href="{{ route('admin.disciplines.index') }}" class="btn btn-outline-secondary shadow-sm px-3">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    {{-- ERROS --}}
    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <h5 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> Atenção</h5>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CARD --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-info-circle me-2"></i> Informações da Disciplina
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.disciplines.update', $discipline->id) }}">
                @csrf
                @method('PUT')

                {{-- CURSO --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="course_id" class="form-select @error('course_id') is-invalid @enderror">
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}"
                                {{ $discipline->course_id == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- NOME --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Nome da Disciplina</label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name', $discipline->name) }}"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Ex: Introdução à Programação"
                        required
                    >
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- CARGA HORÁRIA --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Carga Horária (opcional)</label>
                    <input 
                        type="number" 
                        name="workload" 
                        value="{{ old('workload', $discipline->workload) }}"
                        class="form-control @error('workload') is-invalid @enderror"
                    >
                    @error('workload')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- ORDEM --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Ordem no Curso (opcional)</label>
                    <input 
                        type="number" 
                        name="sequence_order" 
                        value="{{ old('sequence_order', $discipline->sequence_order) }}"
                        class="form-control @error('sequence_order') is-invalid @enderror"
                    >
                    @error('sequence_order')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- BOTÃO SALVAR --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-lg me-2"></i> Salvar Alterações
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
