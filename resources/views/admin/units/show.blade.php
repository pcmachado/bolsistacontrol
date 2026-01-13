@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2> Visualizar Unidade</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('admin.units.index') }}"> Voltar</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Nome:</strong>
            {{ $unit->name }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Cidade:</strong>
            {{ $unit->city }}
        </div>
    </div>
    <div class="form-group">
        <strong>Instituição:</strong>
        {{ $unit->institution->name }}
    </div>
    <div class="form-group">
        <strong>Endereço:</strong>
        {{ $unit->address }}
    </div>
@endsection