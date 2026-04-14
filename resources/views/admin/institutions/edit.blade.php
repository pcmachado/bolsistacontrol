@extends('layouts.app')

@section('title', 'Editar Instituição')

@section('content')

<div class="container-fluid">
    <h1 class="mb-4">Editar Instituição</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.institutions.update', $institution) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nome da Instituição</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        class="form-control @error('name') is-invalid @enderror" 
                        value="{{ old('name', $institution->name) }}" 
                        required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">Salvar Alterações</button>
                <a href="{{ route('admin.institutions.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
@endsection