@extends('layouts.app')

@section('title', 'Editar Projeto')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">
        Editar Projeto: <strong>{{ $project->name }}</strong>
    </h4>

    <div class="row">
        <div class="col-md-3">
            @include('admin.projects.edit._nav', ['active' => 'general'])
        </div>

        <div class="col-md-9">
            <div class="card rounded-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Use o menu lateral para ajustar os dados gerais, cargos, bolsistas, cursos e fontes de fomento do projeto.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
