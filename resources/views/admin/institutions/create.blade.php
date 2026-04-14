@extends('layouts.app')

@section('title', 'Criar Nova Instituição')

@section('content')

<div class="container-fluid">
    <h1 class="mb-4">Criar Nova Instituição</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.institutions.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nome da Instituição</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        class="form-control @error('name') is-invalid @enderror" 
                        value="{{ old('name') }}" 
                        required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">Salvar Instituição</button>
                <a href="{{ route('admin.institutions.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection