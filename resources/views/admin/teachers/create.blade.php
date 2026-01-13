@extends('layouts.app')

@section('title', 'Novo Professor')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Novo Professor</h2>

        <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.teachers.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Senha</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary px-4">
                        <i class="bi bi-check2-circle me-2"></i> Criar Professor
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
