@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Criar Novo Usuário</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary btn-sm mb-2" href="{{ route('admin.users.index') }}"><i class="fa fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
</div>

@if (count($errors) > 0)
    <div class="alert alert-danger">
      <strong>Erro!</strong> Há problemas com os dados inseridos.<br><br>
      <ul>
         @foreach ($errors->all() as $error)
           <li>{{ $error }}</li>
         @endforeach
      </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Nome:</strong>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nome completo" class="form-control" required>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>E-mail:</strong>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="E-mail" class="form-control" required>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Senha:</strong>
                <input type="password" name="password" placeholder="Senha" class="form-control" required>
                <small class="form-text text-muted">Se marcar "Notificar usuário", esta senha será substituída por uma temporária.</small>
            </div>
        </div>
        @if(Auth::user()->hasRole(['admin', 'coordenador_geral']))
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Unidade:</strong>
                <select name="unit_id" class="form-control">
                    <option value="">Selecione uma unidade (opcional)</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif
        <div class="col-xs-12 col-sm-12 col-md-12">
            <x-role-selector
                :selectedRoles="old('role') ? [old('role')] : []"
                :user="auth()->user()"
                :multiple="false"
                :required="true"
                name="role"
            />
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group form-check">
                <input type="checkbox" name="notify_user" value="1" class="form-check-input" id="notify_user">
                <label class="form-check-label" for="notify_user">
                    <strong>Notificar usuário por e-mail</strong>
                </label>
                <br>
                <small class="form-text text-muted">Se marcado, uma senha temporária será gerada e enviada por e-mail para o usuário.</small>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3"><i class="fa-solid fa-floppy-disk"></i> Criar Usuário</button>
        </div>
    </div>
</form>
@endsection
