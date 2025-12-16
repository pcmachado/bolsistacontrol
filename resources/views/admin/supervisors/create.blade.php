@extends('layouts.app')

@section('title', 'Novo Vínculo de Supervisor')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Novo Vínculo</h2>

        <a href="{{ route('admin.supervisors.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('admin.supervisors.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Supervisor</label>
                    <select name="supervisor_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($supervisors as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="course_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Unidade</label>
                    <select name="unit_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary px-4">
                        <i class="bi bi-check2-circle me-2"></i> Salvar
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
