@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
      <h2>Olá, {{ Auth::user()->name }}</h2>
    <h1 class="mb-4">Este é o seu painel Dashboard</h1>

    <div class="card">
        <div class="card-body">
            <p>Bem-vindo, {{ auth()->user()->name }}!</p>
              <div class="row">
                <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                    <h5 class="card-title">Resumo</h5>
                    <p>Informações resumidas (horas, bolsas, etc.)</p>
                    </div>
                </div>
                </div>

                <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                    <h5 class="card-title">Notificações</h5>
                    <p>Nenhuma notificação.</p>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
