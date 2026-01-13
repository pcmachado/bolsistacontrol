@extends('layouts.app')

@section('title', 'Cadastrar Curso')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-journal-plus me-2"></i> Cadastrar Novo Curso
        </h2>

        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary shadow-sm px-3">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    {{-- Exibir erros --}}
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

    {{-- Card do formulário --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-pencil-square me-2"></i> Informações do Curso
            </h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.courses.store') }}">
                @csrf
                
                {{-- Nome do curso --}}
                <div class="mb-4">
                    <label for="name" class="form-label fw-semibold">Nome do Curso</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name"
                        value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Ex: Curso de Informática"
                        required
                    >
                    
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Botão --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-2"></i> Salvar Curso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
