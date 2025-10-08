@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2> Visualizar Cargo</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('admin.positions.index') }}"> Voltar</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Nome:</strong>
            {{ $position->name }}
        </div>
    </div>
</div>
@endsection