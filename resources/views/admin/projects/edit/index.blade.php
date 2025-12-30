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
                   class="list-group-item list-group-item-action">
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
                    <p class="text-muted">
                        Selecione uma seção ao lado para editar os dados do projeto.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
