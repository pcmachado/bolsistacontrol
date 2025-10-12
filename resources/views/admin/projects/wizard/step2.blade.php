@extends('layouts.app')

<style>
    .nav-pills .nav-link.completed {
        background-color: #198754; /* verde */
        color: #fff;
    }
</style>

@section('content')
<div class="container">
    <h3>Passo 2: Definir Cargos</h3>
    @include('admin.projects.partials._steps', ['step' => 2, 'project' => $project ?? null])
    @include('admin.projects.partials._progress', ['progress' => 50, 'label' => 'Passo 2 de 4'])
    <form method="POST" action="{{ route('admin.projects.store.step2', $project) }}">
        @csrf
        <div class="mb-3">
            <label for="positions" class="form-label">Cargos</label>
            <div class="input-group">
                <select name="positions[]" class="form-select" multiple>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addPositionModal">
                    + Novo
                </button>
            </div>
            <small class="text-muted">Segure CTRL (ou CMD no Mac) para selecionar mais de um cargo.</small>
        </div>
        <button type="submit" class="btn btn-primary">Avançar</button>
    </form>
</div>

{{-- Modal genérico para adicionar cargo --}}
<x-modal-add-generic 
    id="addPositionModal"
    title="Adicionar Cargo"
    :route="route('admin.positions.store')"
    :fields="[
        ['name' => 'name', 'label' => 'Nome do Cargo', 'type' => 'text', 'required' => true],
        ['name' => 'hourly_rate', 'label' => 'Valor da Bolsa', 'type' => 'number'],
        ['name' => 'weekly_hour_limit', 'label' => 'Carga Horária Semanal', 'type' => 'number']
    ]"
/>
@endsection
