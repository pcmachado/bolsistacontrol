@extends('layouts.project-wizard')

@section('title', 'Projeto')

@php
    /**
     * Quando $project existir → modo edição (continuar)
     * Quando não existir → modo criação
     */
    $isEdit = isset($project);
@endphp

@section('wizard-content')

<h4 class="mb-4 fw-bold">
    {{ $isEdit ? 'Editar Projeto' : 'Criar Projeto' }}
</h4>

<form method="POST"
      action="{{ $isEdit
            ? route('admin.projects.update.step1', $project)
            : route('admin.projects.store.step1') }}">
    @csrf

    <div class="row g-3">

        {{-- Nome --}}
        <div class="col-md-6">
            <label class="form-label">Nome do Projeto</label>
            <input type="text"
                   name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $project->name ?? '') }}"
                   required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Unidade --}}
        <div class="col-md-3">
            <label class="form-label">Unidade</label>
            <select name="unit_id"
                    class="form-select @error('unit_id') is-invalid @enderror"
                    required>
                <option value="">Selecione</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}"
                        {{ (old('unit_id', $project->unit_id ?? null) == $unit->id) ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
            @error('unit_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Instituição --}}
        <div class="col-md-3">
            <label class="form-label">Instituição</label>
            <select name="institution_id"
                    class="form-select @error('institution_id') is-invalid @enderror"
                    required>
                <option value="">Selecione</option>
                @foreach($institutions as $institution)
                    <option value="{{ $institution->id }}"
                        {{ (old('institution_id', $project->institution_id ?? null) == $institution->id) ? 'selected' : '' }}>
                        {{ $institution->name }}
                    </option>
                @endforeach
            </select>
            @error('institution_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Data início --}}
        <div class="col-md-3">
            <label class="form-label">Data de Início</label>
            <input type="date"
                   name="start_date"
                   class="form-control @error('start_date') is-invalid @enderror"
                   value="{{ old('start_date', $project->start_date ?? '') }}"
                   required>
            @error('start_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Data fim --}}
        <div class="col-md-3">
            <label class="form-label">Data de Término</label>
            <input type="date"
                   name="end_date"
                   class="form-control @error('end_date') is-invalid @enderror"
                   value="{{ old('end_date', $project->end_date ?? '') }}">
            @error('end_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>

    {{-- Ações --}}
    <div class="d-flex justify-content-between mt-4">

        <a href="{{ route('admin.projects.index') }}"
           class="btn btn-outline-secondary">
            ← Voltar para lista
        </a>

        <button type="submit" class="btn btn-primary">
            Próximo →
        </button>

    </div>

</form>

@endsection
