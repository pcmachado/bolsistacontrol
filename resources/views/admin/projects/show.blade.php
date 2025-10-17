@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2> Visualizar Projeto</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('admin.projects.index') }}"> Voltar</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Nome:</strong>
            {{ $project->name }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Email:</strong>
            {{ $project->description }}
        </div>
    </div>
    <div class="form-group">
        <strong>Instituição:</strong>
        {{ $project->institution->name ?? 'Sem instituição' }}
    </div>
@endsection