@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Editar Usuário</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary btn-sm mb-2" href="{{ route('admin.users.index') }}"><i class="fa fa-arrow-left"></i> Voltar</a>
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

<form method="POST" action="{{ route('admin.users.update', $user->id) }}">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Nome:</strong>
                <input type="text" name="name" placeholder="Nome" class="form-control" value="{{ $user->name }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Email:</strong>
                <input type="email" name="email" placeholder="Email" class="form-control" value="{{ $user->email }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="mb-3">
                <label for="role" class="form-label"><strong>Papel</strong></label>
                <select name="role" class="form-select" required>
                    <option value="">Selecione um Papel</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}"
                            {{ old('role', $user->getRoleNames()->first() ?? '') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>

                @error('role')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
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
                            {{ (old('unit_id', $user->unit_id ?? '') == $unit->id) ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                    @endforeach
                </select>

                @if(auth()->user()->hasRole(['admin','coordenador-geral']))
                    <input type="hidden" name="unit_id" value="{{ old('unit_id', $user->unit_id ?? '') }}">
                @endif
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Senha:</strong>
                <input type="password" name="password" placeholder="Senha" class="form-control">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Confirmar Senha:</strong>
                <input type="password" name="confirm-password" placeholder="Confirmar Senha" class="form-control">
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