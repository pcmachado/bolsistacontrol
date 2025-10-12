@extends('layouts.app')

@section('title', 'Área do Bolsista')

@section('content')
    <h3>Bem-vindo, {{ auth()->user()->name }}</h3>
    <p>Utilize o botão abaixo para gerar o seu relatório mensal de atividades:</p>

    <form method="GET" action="{{ route('reports.myReport') }}" class="d-inline">
        <div class="row g-3">
            <div class="col-md-2">
                <label for="month" class="form-label">Mês</label>
                <input type="number" name="month" id="month" class="form-control"
                       value="{{ now()->month }}" min="1" max="12">
            </div>
            <div class="col-md-2">
                <label for="year" class="form-label">Ano</label>
                <input type="number" name="year" id="year" class="form-control"
                       value="{{ now()->year }}">
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-file-earmark-pdf"></i> Gerar meu relatório mensal
                </button>
            </div>
        </div>
    </form>
    {{-- Card extra: Minhas Pendentes --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-info shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-person-check display-5 text-info"></i>
                    <h5 class="card-title mt-2">Meus Registros Pendentes</h5>
                    <h2 class="text-info">{{ $myPending }}</h2>
                </div>
            </div>
        </div>
    </div>
@endsection
