@extends('layouts.app')

@section('title', 'Editar Professor')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-pencil me-2"></i> Editar Professor</h2>

        <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">
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
            <form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $teacher->name) }}"
                           class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email', $teacher->email) }}"
                           class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Senha (opcional)</label>
                    <input type="password" name="password" class="form-control">
                    <small class="text-muted">Preencha somente para alterar a senha</small>
                </div>

                <div class="text-end">
                    <button class="btn btn-success px-4">
                        <i class="bi bi-check2-circle me-2"></i> Atualizar
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
