<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('content')
<div class="row text-center mb-4">
    <div class="col">
        <h1>Dashboard Administrativo</h1>
    </div>
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-info">
            <div class="card-body">
                <h5 class="card-title text-info">Total de Bolsistas</h5>
                <h3 class="card-text">{{ $totalBolsistas }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-success">
            <div class="card-body">
                <h5 class="card-title text-success">Unidades Ativas</h5>
                <h3 class="card-text">{{ $totalUnidades }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-warning">
            <div class="card-body">
                <h5 class="card-title text-warning">Notificações Pendentes</h5>
                <h3 class="card-text">{{ $notificacoesPendentes }}</h3>
            </div>
        </div>
    </div>
</div>
@endsection