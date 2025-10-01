@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/css/login.css') }}">
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="login-card">
                <div class="card shadow-lg border-0 rounded-4">
                    
                    <!-- Header do Card de Login -->
                    <div class="card-header bg-white border-bottom text-center pt-4 pb-3">
                        <i class="bi bi-people-fill text-primary display-4 mb-2"></i>
                        <h4 class="fw-bold text-dark mb-0">{{ __('Acesso ao Sistema de Bolsistas') }}</h4>
                        <p class="text-muted small mb-0">{{ __('Insira suas credenciais para continuar.') }}</p>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        
                        <!-- Formulário de Login -->
                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <!-- Campo Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">{{ __('Endereço de E-mail') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control rounded-end" id="email" name="email" placeholder="{{ __('seu@email.com') }}" required>
                                </div>
                            </div>
                            
                            <!-- Campo Senha -->
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">{{ __('Senha') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control rounded-end" id="password" name="password" placeholder="{{ __('********') }}" required>
                                </div>
                            </div>
                            
                            <!-- Opções Adicionais (Lembrar-me e Esqueci a Senha) -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                                    <label class="form-check-label" for="rememberMe">
                                        {{ __('Lembrar-me') }}
                                    </label>
                                </div>
                                <a href="{{ route('password.request') }}" class="text-decoration-none small text-secondary">{{ __('Esqueceu a Senha?') }}</a>
                            </div>
                            
                            <!-- Botão de Login -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg rounded-3 shadow-sm">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> {{ __('Entrar') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Footer do Card (Link para Registro) -->
                    <div class="card-footer bg-light border-top text-center p-3 rounded-bottom-4">
                        <p class="small text-muted mb-0">
                            {{ __('Ainda não tem uma conta?') }}
                            <a href="#" class="text-decoration-none fw-semibold text-primary">{{ __('Crie sua conta aqui!') }}</a>
                        </p>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
