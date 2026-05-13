@extends('layouts.app')

@section('title', 'Forma de Fomento')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Forma de Fomento</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.funding-sources.edit', $fundingSource) }}" class="btn btn-primary">Editar</a>
            <a href="{{ route('admin.funding-sources.index') }}" class="btn btn-outline-secondary">Voltar</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-md-3">Nome</dt>
                <dd class="col-md-9">{{ $fundingSource->name }}</dd>

                <dt class="col-md-3">Código</dt>
                <dd class="col-md-9">{{ $fundingSource->code ?? '-' }}</dd>

                <dt class="col-md-3">Tipo</dt>
                <dd class="col-md-9">{{ $fundingSource->type === 'internal' ? 'Interna' : 'Externa' }}</dd>

                <dt class="col-md-3">Valor Total</dt>
                <dd class="col-md-9">R$ {{ number_format($fundingSource->total_amount ?? 0, 2, ',', '.') }}</dd>

                <dt class="col-md-3">Status</dt>
                <dd class="col-md-9">{{ $fundingSource->active ? 'Ativa' : 'Inativa' }}</dd>

                <dt class="col-md-3">Descrição</dt>
                <dd class="col-md-9">{{ $fundingSource->description ?? '-' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
