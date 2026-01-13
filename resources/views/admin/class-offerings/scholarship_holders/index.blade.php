@extends('layouts.app')

@section('title', 'Bolsistas da Turma')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="bi bi-people me-2"></i> Bolsistas da Turma: {{ $offering->name ?? 'Sem nome' }}
        </h2>

        <a href="{{ route('admin.class-offerings.index') }}" class="btn btn-outline-secondary px-3">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    {{-- Alerts --}}
    @foreach(['success','warning','error'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg }} shadow-sm">{{ session($msg) }}</div>
        @endif
    @endforeach

    {{-- Formulário de vínculo --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-plus-circle me-2"></i> Adicionar Bolsista à Turma
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.class-offerings.scholarship_holders.store', $offering->id) }}">
                @csrf
                <div class="row g-3 align-items-end">

                    <div class="col-md-5">
                        <label class="form-label">Bolsista</label>
                        <select name="scholarship_holder_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            @foreach($available as $h)
                                <option value="{{ $h->id }}">{{ $h->user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Papel (opcional)</label>
                        <input type="text" name="role" class="form-control" placeholder="Ex: Aluno, Monitor">
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-check2-circle me-2"></i> Adicionar
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Lista de bolsistas --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold">
            <i class="bi bi-list-task me-2"></i> Bolsistas Vinculados
        </div>

        <div class="card-body">
            {!! $dataTable->table(['class' => 'table table-striped table-hover w-100'], true) !!}
        </div>
    </div>
</div>

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush

@endsection
