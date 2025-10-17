@extends('layouts.app')

    <style>
        .nav-pills .nav-link.completed {
            background-color: #198754; /* verde */
            color: #fff;
        }
    </style>

@section('content')
<div class="container">
    <h3>Revisão Final do Projeto</h3>
    @include('admin.projects.partials._steps', ['step' => 6, 'project' => $project ?? null])
    @include('admin.projects.partials._progress', ['progress' => 100, 'label' => 'Revisão Final'])

    <div class="card mb-4">
        <div class="card-header">Checklist de Conclusão</div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Projeto
                    <span class="text-success fw-bold">✅</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Cargos
                    <span class="text-success fw-bold">✅</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Bolsistas
                    <span class="text-success fw-bold">✅</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Cursos
                    <span class="text-success fw-bold">✅</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Fontes de Fomento
                    <span class="text-success fw-bold">✅</span>
                </li>
            </ul>
        </div>
    </div>
    <p>Revise todas as informações do projeto antes de finalizar.</p>

    <div class="card mb-3">
        <div class="card-header">Informações do Projeto</div>
        <div class="card-body">
            <p><strong>Nome:</strong> {{ $project->name }}</p>
            <p><strong>Descrição:</strong> {{ $project->description }}</p>
            <p><strong>Data de Início:</strong> {{ $project->start_date }}</p>
            <p><strong>Data de Término:</strong> {{ $project->end_date }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Cargos</div>
        <div class="card-body">
            <ul>
                @foreach($project->positions as $pos)
                    <li>{{ $pos->name }} (Bolsa: {{ $pos->hourly_rate }}, Horas: {{ $pos->weekly_hour_limit }})</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Bolsistas</div>
        <div class="card-body">
            <ul>
                @foreach($project->scholarshipHolders as $holder)
                    <li>{{ $holder->name }} — {{ $holder->pivot->status }} ({{ $holder->pivot->start_date }} até {{ $holder->pivot->end_date }})</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Cursos</div>
        <div class="card-body">
            <ul>
                @foreach($project->courses as $course)
                    <li>{{ $course->name }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Fontes de Fomento</div>
        <div class="card-body">
            <ul>
                @foreach($project->fundingSources as $funding)
                    <li>{{ $funding->name }} — R$ {{ number_format($funding->pivot->amount, 2, ',', '.') }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.projects.finalize', $project) }}">
        @csrf
        <button type="submit" class="btn btn-success">Finalizar Projeto</button>
        <a href="{{ route('admin.projects.create.step5', $project) }}" class="btn btn-secondary">Voltar</a>
    </form>
</div>
@endsection
