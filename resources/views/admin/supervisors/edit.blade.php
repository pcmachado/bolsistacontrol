@extends('layouts.app')

@section('title', 'Editar Vínculo')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar Vínculo</h2>

        <a href="{{ route('admin.supervisors.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul>
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.supervisors.update', $assignment->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Supervisor</label>
                    <select name="supervisor_id" class="form-select" required>
                        @foreach($supervisors as $s)
                            <option value="{{ $s->id }}"
                                {{ $assignment->supervisor_id == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="course_id" class="form-select" required>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}"
                                {{ $assignment->course_id == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Unidade</label>
                    <select name="unit_id" class="form-select" required>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}"
                                {{ $assignment->unit_id == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="active" class="form-select">
                        <option value="1" {{ $assignment->active ? 'selected' : '' }}>Ativo</option>
                        <option value="0" {{ !$assignment->active ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>

                <div class="text-end">
                    <button class="btn btn-success px-4">
                        <i class="bi bi-check2-circle me-2"></i> Salvar Alterações
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
