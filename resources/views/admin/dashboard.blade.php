@extends('layouts.app')

@section('title', 'Dashboard - Admin')

@section('content')
<div class="container-fluid">
  <h1 class="mb-4">Dashboard Administrativo</h1>

  <div class="row">
    <div class="col-md-3">
      <div class="card text-bg-primary mb-3">
        <div class="card-body">
          <h5 class="card-title">Usuários</h5>
          <p class="card-text">{{ $usersCount ?? 0 }} cadastrados</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-bg-success mb-3">
        <div class="card-body">
          <h5 class="card-title">Bolsistas</h5>
          <p class="card-text">{{ $scholarshipHoldersCount ?? 0 }} ativos</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
