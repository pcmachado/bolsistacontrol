@extends('layouts.app')

@section('title', 'Editar Fonte de Recursos')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Editar Fonte de Recursos</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.funding-sources.update', $fundingSource) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name', $fundingSource->name) }}" 
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Digite o nome da fonte de recurso...">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button class="btn btn-primary">Salvar Alterações</button>
                <a href="{{ route('admin.funding-sources.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection