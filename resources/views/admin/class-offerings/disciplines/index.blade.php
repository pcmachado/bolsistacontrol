@extends('layouts.app')

@section('title', 'Disciplinas da Turma')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="bi bi-diagram-3 me-2"></i> Disciplinas da Turma: {{ $offering->name ?? 'Sem nome' }}
        </h2>

        <a href="{{ route('admin.class-offerings.index') }}" class="btn btn-outline-secondary px-3">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    {{-- Alerts --}}
    @foreach(['success', 'warning', 'error'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg }} shadow-sm">
                {{ session($msg) }}
            </div>
        @endif
    @endforeach

    {{-- Vínculo --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-plus-circle me-2"></i> Adicionar Disciplina à Turma
        </div>

        <div class="card-body">
            <form action="{{ route('admin.class-offerings.disciplines.store', $offering->id) }}" method="POST">
                @csrf

                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Disciplina</label>
                        <select name="discipline_id" class="form-select">
                            @foreach($disciplines as $disc)
                                <option value="{{ $disc->id }}">{{ $disc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i> Adicionar
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- Listagem --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-journal-text me-2"></i> Disciplinas Vinculadas
        </div>

        <div class="card-body p-0">
            @include('admin.class-offerings.disciplines.partials.list', [
                'offering' => $offering,
                'teachers' => $teachers
            ])
        </div>
    </div>
</div>
@endsection
