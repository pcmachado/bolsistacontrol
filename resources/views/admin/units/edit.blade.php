@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Editar Unidade</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary btn-sm mb-2" href="{{ route('admin.units.index') }}"><i class="fa fa-arrow-left"></i> Voltar</a>
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

<form method="POST" action="{{ route('admin.units.update', $unit->id) }}">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Nome:</strong>
                <input type="text" name="name" placeholder="Nome" class="form-control" value="{{ $unit->name }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Endereço:</strong>
                <input type="text" name="address" placeholder="Endereço" class="form-control" value="{{ $unit->address }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Cidade:</strong>
                <input type="text" name="city" placeholder="Cidade" class="form-control" value="{{ $unit->city }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="mb-3">
                <label for="institution" class="form-label"><strong>Instituição</strong></label>
                <select name="institution" class="form-select" required>
                    <option value="">Selecione uma Instituição</option>
                    @foreach($institutions as $institution)
                        <option value="{{ $institution->id }}"
                            {{ old('institution_id', $unit->institution_id ?? '') == $institution->id ? 'selected' : '' }}>
                            {{ ucfirst($institution->name) }}
                        </option>
                    @endforeach
                </select>
                
                @error('institution')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
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