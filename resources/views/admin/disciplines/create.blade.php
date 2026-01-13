@extends('layouts.app')

@section('title', 'Nova Disciplina')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="bi bi-plus-square me-2"></i> Nova Disciplina
        </h2>

        <a href="{{ route('admin.disciplines.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.disciplines.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="course_id" class="form-select">
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Nome da Disciplina</label>
                    <input type="text" name="name" class="form-control" placeholder="Ex: Programação I">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Carga Horária (opcional)</label>
                    <input type="number" name="workload" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Ordem no Curso (opcional)</label>
                    <input type="number" name="sequence_order" class="form-control">
                </div>

                <div class="text-end">
                    <button class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Salvar
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
