@extends('layouts.app')

<style>
    .nav-pills .nav-link.completed {
        background-color: #198754; /* verde */
        color: #fff;
    }
</style>

@section('content')
<div class="container">
    <h3>Passo 3: Vincular Bolsistas</h3>
    @include('admin.projects.partials._steps', ['step' => 3, 'project' => $project ?? null])
    @include('admin.projects.partials._progress', ['progress' => 75, 'label' => 'Passo 3 de 4'])
    <form method="POST" action="{{ route('admin.projects.store.step3', $project) }}">
        @csrf
        <div class="mb-3">
            <label>Bolsista</label>
            <select name="scholarships[0][holder_id]" class="form-control">
                @foreach($holders as $holder)
                    <option value="{{ $holder->id }}">{{ $holder->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Cargo</label>
            <select name="scholarships[0][position_id]" class="form-control">
                @foreach($positions as $pos)
                    <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Avançar</button>
    </form>
</div>
@endsection
