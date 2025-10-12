@extends('layouts.app')

<style>
    .nav-pills .nav-link.completed {
        background-color: #198754; /* verde */
        color: #fff;
    }
</style>

@section('content')
<div class="container">
    <h3>Passo 4: Revisão</h3>
    @include('admin.projects.partials._steps', ['step' => 4, 'project' => $project ?? null])
    @include('admin.projects.partials._progress', ['progress' => 100, 'label' => 'Passo 4 de 4'])
    <p>Revise as informações do projeto antes de finalizar.</p>
    <h5>{{ $project->name }}</h5>
    <ul>
        @foreach($project->positions as $pos)
            <li>{{ $pos->name }} - {{ $pos->workload_hours }}h - R$ {{ $pos->scholarship_value }}</li>
        @endforeach
    </ul>
    <h5>Bolsistas</h5>
    <ul>
        @foreach($project->scholarships as $sch)
            <li>{{ $sch->scholarshipHolder->name }} ({{ $sch->position->name }})</li>
        @endforeach
    </ul>
    <form method="POST" action="{{ route('admin.projects.finish', $project) }}">
        @csrf
        <button type="submit" class="btn btn-success">Confirmar e Ativar Projeto</button>
    </form>
</div>
@endsection
