@extends('layouts.app')

@section('title', 'Editar Projeto')

@section('content')
<div class="container-fluid">

    <h4 class="mb-4">
        Editar Projeto: <strong>{{ $project->name }}</strong>
    </h4>

    <div class="row">
        {{-- Menu lateral --}}
        <div class="col-md-3">
            <div class="list-group rounded-0">
                <a href="{{ route('admin.projects.edit.general', $project) }}"
                   class="list-group-item list-group-item-action active">
                    Dados Gerais
                </a>
                <a href="{{ route('admin.projects.edit.scholars', $project) }}"
                   class="list-group-item list-group-item-action">
                    Bolsistas
                </a>
                <a href="{{ route('admin.projects.edit.courses', $project) }}"
                   class="list-group-item list-group-item-action">
                    Cursos
                </a>
                <a href="{{ route('admin.projects.edit.funding', $project) }}"
                   class="list-group-item list-group-item-action">
                    Fontes de Fomento
                </a>
            </div>
        </div>

        {{-- Conteúdo --}}
        <div class="col-md-9">
            <div class="card rounded-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.projects.update', $project) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nome do Projeto</label>
                            <input type="text" name="name" value="{{ old('name', $project->name) }}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $project->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data de Início</label>
                            <input type="date" name="start_date" value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data de Término</label>
                            <input type="date" name="end_date" value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}" class="form-control">
                        </div>
                        <div class="d-grid gap-2 d-md-block">
                            <button class="btn btn-primary">Salvar Alterações</button>
                            <a href="{{ route('admin.projects.edit.index', $project) }}" class="btn btn-outline-secondary">
                                Voltar
                            </a>
                            <a href="{{ route('admin.projects.destroy', $project) }}" class="btn btn-danger" onclick="event.preventDefault(); if(confirm('Tem certeza que deseja excluir este projeto?')) { document.getElementById('delete-form').submit(); }">
                                Excluir Projeto
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="delete-form" action="{{ route('admin.projects.destroy', $project) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection