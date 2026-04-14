@extends('layouts.app')

@section('title', 'Exibir Instituição')

@section('content')

<div class="container-fluid">
    <h1 class="mb-4">Exibir Instituição</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">{{ $institution->name }}</h5>
            <p class="card-text"><strong>ID:</strong> {{ $institution->id }}</p>
            <p class="card-text"><strong>Nome:</strong> {{ $institution->name }}</p>
            <a href="{{ route('admin.institutions.edit', $institution) }}" class="btn btn-primary">Editar</a>
            <a href="{{ route('admin.institutions.index') }}" class="btn btn-secondary">Voltar para Lista</a>
        </div>
    </div>
</div>
@endsection