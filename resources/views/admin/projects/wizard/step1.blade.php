@extends('layouts.app')

<style>
    .nav-pills .nav-link.completed {
        background-color: #198754; /* verde */
        color: #fff;
    }
</style>

@section('content')
<div class="container">
    <h3>Passo 1: Criar Projeto</h3>
    @include('admin.projects.partials._steps', ['step' => 1, 'project' => $project ?? null])
    @include('admin.projects.partials._progress', ['progress' => 25, 'label' => 'Passo 1 de 4'])
    <form method="POST" action="{{ route('admin.projects.store.step1') }}">
        @csrf
        <div class="mb-3">
            <label>Nome do Projeto</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Instituição</label>
            <select name="instituition_id" class="form-control" required>
                @foreach($instituitions as $inst)
                    <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Unidade</label>
            <select name="unit_id" class="form-control" required>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Data de Início</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Data de Término</label>
            <input type="date" name="end_date" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Avançar</button>
    </form>
</div>
@endsection
