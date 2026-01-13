@extends('layouts.guest')

@section('title', 'Selecionar Instituição')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-lg border-0 rounded-0">
                <div class="card-header text-center bg-white border-bottom-0 pt-4 pb-3">
                    <i class="bi bi-building text-primary display-5 mb-2"></i>
                    <h4 class="fw-bold text-dark mb-0">Selecione sua Instituição</h4>
                    <p class="text-muted small">Bem-vindo, {{ $user->name }}</p>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('institution.set') }}">
                        @csrf
                        <div class="mb-3">
                            <select name="institution_id" class="form-select form-select-lg" required>
                                <option value="">-- Escolha uma Instituição --</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}">
                                        {{ $institution->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-0 shadow-sm">
                                Confirmar
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer text-center bg-light border-top py-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-link text-danger text-decoration-none">
                            <i class="bi bi-box-arrow-left me-1"></i> Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
