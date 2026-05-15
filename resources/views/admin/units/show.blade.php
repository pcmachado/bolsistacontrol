@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div>
                <h1 class="page-title mb-1">
                    Unidade
                </h1>

                <p class="text-muted mb-0">
                    Visualização completa da unidade institucional.
                </p>
            </div>

            <a href="{{ route('admin.units.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Voltar
            </a>

        </div>
    </div>

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="row g-4">

                <div class="col-md-6">
                    <label class="form-label text-muted small">
                        Nome
                    </label>

                    <div class="fw-semibold">
                        {{ $unit->name }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-muted small">
                        Sigla
                    </label>

                    <div class="fw-semibold">
                        {{ $unit->shortname }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-muted small">
                        Instituição
                    </label>

                    <div class="fw-semibold">
                        {{ $unit->institution->name }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label text-muted small">
                        Cidade
                    </label>

                    <div class="fw-semibold">
                        {{ $unit->city }}
                    </div>
                </div>

                <div class="col-md-12">
                    <label class="form-label text-muted small">
                        Endereço
                    </label>

                    <div class="fw-semibold">
                        {{ $unit->address ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-muted small">
                        Telefone
                    </label>

                    <div class="fw-semibold">
                        {{ $unit->phone ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-muted small">
                        Email
                    </label>

                    <div class="fw-semibold">
                        {{ $unit->email ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-muted small">
                        CNPJ
                    </label>

                    <div class="fw-semibold">
                        {{ $unit->cnpj ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-muted small">
                        Unidade Administrativa
                    </label>

                    <div>
                        @if($unit->is_administrative)
                            <span class="badge bg-success">
                                Sim
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                Não
                            </span>
                        @endif
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection