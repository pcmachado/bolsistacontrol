@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div>
                <h1 class="page-title mb-1">
                    Editar Unidade
                </h1>

                <p class="text-muted mb-0">
                    Atualize os dados institucionais da unidade.
                </p>
            </div>

            <a href="{{ route('admin.units.index') }}"
               class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left"></i>
                Voltar

            </a>

        </div>
    </div>

    @if ($errors->any())

        <div class="alert alert-danger border-0 shadow-sm">

            <div class="fw-semibold mb-2">
                Corrija os erros abaixo:
            </div>

            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif

    <form method="POST"
          action="{{ route('admin.units.update', $unit->id) }}">

        @csrf
        @method('PUT')

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-8">
                        <label class="form-label">
                            Nome da unidade
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $unit->name) }}"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Sigla
                        </label>

                        <input
                            type="text"
                            name="shortname"
                            class="form-control"
                            value="{{ old('shortname', $unit->shortname) }}"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Cidade
                        </label>

                        <input
                            type="text"
                            name="city"
                            class="form-control"
                            value="{{ old('city', $unit->city) }}"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Instituição
                        </label>

                        <input
                            type="text"
                            class="form-control bg-light"
                            value="{{ $institution->name }}"
                            disabled
                        >

                    </div>

                    <div class="col-md-12">
                        <label class="form-label">
                            Endereço
                        </label>

                        <input
                            type="text"
                            name="address"
                            class="form-control"
                            value="{{ old('address', $unit->address) }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Telefone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="{{ old('phone', $unit->phone) }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', $unit->email) }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            CNPJ
                        </label>

                        <input
                            type="text"
                            name="cnpj"
                            class="form-control"
                            value="{{ old('cnpj', $unit->cnpj) }}"
                        >
                    </div>

                    <div class="col-md-4">

                        <div class="form-check mt-4">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="is_administrative"
                                value="1"
                                {{ old('is_administrative', $unit->is_administrative) ? 'checked' : '' }}
                            >

                            <label class="form-check-label">
                                Unidade administrativa
                            </label>

                        </div>

                    </div>

                </div>

            </div>

            <div class="card-footer bg-white border-0">

                <div class="d-flex justify-content-end">

                    <button class="btn btn-primary">

                        <i class="bi bi-check-circle"></i>
                        Salvar alterações

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>

@endsection