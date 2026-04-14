@extends('layouts.app')

@section('title', 'Adicionar Oferta de Turma')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Adicionar Oferta de Turma</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.courses.class-offerings.store', $course) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Semestre</label>
                    <input 
                        type="text" 
                        name="semester" 
                        value="{{ old('semester') }}" 
                        class="form-control @error('semester') is-invalid @enderror"
                        placeholder="Ex: 2024.1">
                    @error('semester')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Ano</label>
                    <input 
                        type="number" 
                        name="year" 
                        value="{{ old('year') }}" 
                        class="form-control @error('year') is-invalid @enderror"
                        placeholder="Ex: 2024">
                    @error('year')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button class="btn btn-primary">Adicionar Oferta</button>
                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection