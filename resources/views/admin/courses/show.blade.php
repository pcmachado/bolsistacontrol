@extends('layouts.app')

@section('title', 'Visualizar Curso')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-eye me-2"></i> Visualizar Curso</h2>
            <p class="text-muted mb-0">Detalhes completos do curso cadastrado.</p>
        </div>
        <a class="btn btn-outline-secondary shadow-sm" href="{{ route('admin.courses.index') }}">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Nome</dt>
                        <dd class="col-sm-8">{{ $course->name }}</dd>

                        <dt class="col-sm-4">Projeto(s)</dt>
                        <dd class="col-sm-8">
                            {{ $course->projects->pluck('name')->implode(', ') ?: 'Nenhum projeto vinculado' }}
                        </dd>

                        <dt class="col-sm-4">Instituição</dt>
                        <dd class="col-sm-8">{{ $course->institution?->name ?? 'Nenhuma' }}</dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">{{ $course->active ? 'Ativo' : 'Inativo' }}</dd>

                        <dt class="col-sm-4">Carga Horária</dt>
                        <dd class="col-sm-8">{{ $course->duration_hours ?? '-' }} horas</dd>

                        <dt class="col-sm-4">Período</dt>
                        <dd class="col-sm-8">
                            {{ $course->start_date ? $course->start_date->format('d/m/Y') : '-' }}
                            @if($course->end_date)
                                até {{ $course->end_date->format('d/m/Y') }}
                            @endif
                        </dd>

                        <dt class="col-sm-4">Pré-requisitos</dt>
                        <dd class="col-sm-8">{{ $course->prerequisites ?: '-' }}</dd>

                        <dt class="col-sm-4">Descrição</dt>
                        <dd class="col-sm-8">{{ $course->description ?: '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold">Disciplinas do Curso</h5>
                </div>
                <div class="card-body">
                    @if($course->disciplines->isEmpty())
                        <p class="text-muted mb-0">Nenhuma disciplina vinculada ainda.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($course->disciplines as $discipline)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <strong>{{ $discipline->name }}</strong>
                                        <div class="text-muted small">{{ $discipline->code ?? 'Sem código' }}</div>
                                    </span>
                                    <span class="badge bg-secondary rounded-pill">
                                        {{ $discipline->active ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 mt-3">
        <div class="form-group">
            <strong>Capacidade de Alunos:</strong>
            {{ $course->capacity ?? 'Não informada' }}
        </div>
    </div>
</div>
@endsection
