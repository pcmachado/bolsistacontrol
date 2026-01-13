@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Editar Projeto</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary btn-sm mb-2" href="{{ route('admin.projects.index') }}"><i class="fa fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
</div>

@if (count($errors) > 0)
    <div class="alert alert-danger">
      <strong>Whoops!</strong> Ocorreu um problema com sua entrada.<br><br>
      <ul>
         @foreach ($errors->all() as $error)
           <li>{{ $error }}</li>
         @endforeach
      </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.projects.update', $project->id) }}">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Nome:</strong>
                <input type="text" name="name" placeholder="Nome" class="form-control" value="{{ $project->name }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Descrição:</strong>
                <input type="text" name="description" placeholder="Descrição" class="form-control" value="{{ $project->description }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="mb-3">
                <label for="unit" class="form-label"> <strong>Unidade</strong></label>
                <select name="unit_id" id="unit" class="form-select select2"
                    @if(auth()->user()->hasRole(['admin','coordenador-geral'])) disabled @endif>
                    <option value="">Selecione</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}"
                            {{ (old('unit_id', $project->unit_id ?? '') == $unit->id) ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="mb-3">
                <label for="institution" class="form-label"> <strong>Instituição</strong></label>
                <select name="institution_id" id="institution" class="form-select select2" >
                    <option value="">Selecione</option>
                    @foreach($institutions as $institution)
                        <option value="{{ $institution->id }}"
                            {{ (old('institution_id', $project->institution_id ?? '') == $institution->id) ? 'selected' : '' }}>
                            {{ $institution->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Início:</strong>
                <input type="date" name="start_date" class="form-control" value="{{ $project->start_date }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Final:</strong>
                <input type="date" name="end_date" class="form-control" value="{{ $project->end_date }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3"><i class="fa-solid fa-floppy-disk"></i> Salvar alterações</button>
        </div>
    </div>
</form>

@endsection
<script>
$(document).ready(function() {
    $('#unit').select2({
        placeholder: "Selecione uma unidade",
        allowClear: true,
        width: '100%'
    });
});
</script>