@extends('layouts.guest')

@section('title', 'Entrar | ProBolsas')

@section('content')
<style>
    .login-split { min-height: calc(100vh - 120px); }
    .brand-side { background:#17357d; color:#fff; position:relative; overflow:hidden; }
    .brand-side::before,.brand-side::after{content:'';position:absolute;border-radius:50%;background:rgba(255,255,255,.08)}
    .brand-side::before{width:320px;height:320px;left:-140px;top:-60px}
    .brand-side::after{width:380px;height:380px;right:-180px;bottom:-180px}
    .login-panel { background:#f2f4f7; }
    .login-card { max-width:480px; width:100%; }
</style>
<div class="container-fluid px-0 login-split d-flex">
    <div class="col-lg-6 d-none d-lg-flex brand-side align-items-center justify-content-center p-5">
        <div style="max-width:440px; z-index:1;">
            <h2 class="fw-bold mb-3">Sistema Integrado de Gestão Acadêmica</h2>
            <p class="mb-4 opacity-75">Submissão, acompanhamento e controle de fluxos institucionais com acesso unificado.</p>
            <ul class="list-unstyled small">
                <li class="mb-2">✅ Login único institucional</li>
                <li class="mb-2">✅ Gestão de perfis e permissões</li>
                <li>✅ Controle de prazos e atividades</li>
            </ul>
        </div>
    </div>

    <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center login-panel p-4">
        <div class="login-card">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h3 class="fw-bold text-center mb-2">Bem-vindo ao ProBolsas</h3>
                    <p class="text-center text-muted mb-4">Informe suas credenciais para acessar</p>

                    @if($errors->has('oauth'))
                        <div class="alert alert-warning">{{ $errors->first('oauth') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Usuário ou E-mail</label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Manter conectado neste dispositivo</label>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-success btn-lg">Entrar no Sistema</button>
                        </div>
                    </form>

                    @if(config('services.ifrs_login.enabled'))
                        <div class="text-center text-muted small mb-3">ACESSO RÁPIDO EXTERNO</div>
                        <div class="d-grid">
                            <a href="{{ route('login.ifrs') }}" class="btn btn-success">Acessar com Conta IFRS</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
