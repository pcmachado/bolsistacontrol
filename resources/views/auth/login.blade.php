@extends('layouts.guest')

@section('title', 'Entrar | ProBolsas')

@section('content')

<style>

    :root{
        --probolsas-blue:#032454;
        --probolsas-blue-light:#0b3f7a;
        --probolsas-green:#49b35d;
        --probolsas-bg:#f4f6f9;
    }

    .login-split{
        min-height:calc(100vh - 110px);
    }

    .brand-side{
        background:
            linear-gradient(
                145deg,
                #032454 0%,
                #0b3f7a 100%
            );

        color:#fff;
        position:relative;
        overflow:hidden;
    }

    .brand-side::before,
    .brand-side::after{
        content:'';
        position:absolute;
        border-radius:50%;
        background:rgba(255,255,255,.05);
    }

    .brand-side::before{
        width:420px;
        height:420px;
        left:-180px;
        top:-120px;
    }

    .brand-side::after{
        width:520px;
        height:520px;
        right:-260px;
        bottom:-260px;
    }

    .login-panel{
        background:var(--probolsas-bg);
    }

    .login-card{
        max-width:480px;
        width:100%;
    }

    .login-card .card{
        border:none;
        border-radius:24px;
        overflow:hidden;
        box-shadow:
            0 10px 30px rgba(0,0,0,.08);
    }

    .logo-login{
        max-width:220px;
    }

    .btn-probolsas{
        background:var(--probolsas-blue);
        border:none;
        color:#fff;
    }

    .btn-probolsas:hover{
        background:#021a3d;
        color:#fff;
    }

    .btn-ifrs{
        background:#198754;
        border:none;
        color:#fff;
    }

    .btn-ifrs:hover{
        background:#157347;
        color:#fff;
    }

    .feature-item{
        background:rgba(255,255,255,.08);
        border:1px solid rgba(255,255,255,.08);
        border-radius:18px;
        padding:16px 18px;
        margin-bottom:16px;
        backdrop-filter: blur(6px);
    }

    .feature-item h6{
        font-weight:600;
        margin-bottom:6px;
    }

    .feature-item p{
        margin:0;
        opacity:.80;
        font-size:.92rem;
    }

</style>

<div class="container-fluid px-0 login-split d-flex">

    {{-- Painel login --}}
    <div
        class="col-12 col-lg-6 d-flex align-items-center justify-content-center login-panel p-4"
    >

        <div class="login-card">

            <div class="card">

                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">

                        <img
                            src="{{ asset('images/probolsas_fundo_escuro.png') }}"
                            alt="ProBolsas"
                            class="img-fluid logo-login mb-3"
                        >

                        <h3 class="fw-bold mb-2">
                            Acesso à Plataforma
                        </h3>

                        <p class="text-muted mb-0">
                            Gestão administrativa de bolsas,
                            frequências e pagamentos institucionais.
                        </p>

                    </div>

                    @if($errors->has('oauth'))
                        <div class="alert alert-warning rounded-3">
                            {{ $errors->first('oauth') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">

                        @csrf

                        <div class="mb-3">

                            <label for="email" class="form-label fw-semibold">
                                Usuário ou E-mail
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                required
                                autofocus
                            >

                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="mb-3">

                            <label for="password" class="form-label fw-semibold">
                                Senha
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control form-control-lg rounded-3 @error('password') is-invalid @enderror"
                                required
                            >

                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">

                            <div class="form-check">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="remember"
                                    id="remember"
                                >

                                <label
                                    class="form-check-label"
                                    for="remember"
                                >
                                    Manter conectado
                                </label>

                            </div>

                            <a
                                href="{{ route('password.request') }}"
                                class="small text-decoration-none"
                            >
                                Recuperar acesso
                            </a>

                        </div>

                        <div class="d-grid mb-4">

                            <button
                                type="submit"
                                class="btn btn-probolsas btn-lg rounded-pill"
                            >
                                Entrar no Sistema
                            </button>

                        </div>

                    </form>

                    @if(config('services.ifrs.enabled'))

                        <div class="text-center mb-3">

                            <span
                                class="small text-uppercase text-muted fw-semibold"
                            >
                                Login Institucional
                            </span>

                        </div>

                        <div class="d-grid">

                            <a
                                href="{{ route('login.ifrs.redirect') }}"
                                class="btn btn-ifrs btn-lg rounded-pill"
                            >
                                Entrar com Conta IFRS
                            </a>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

    {{-- Painel institucional --}}
    <div
        class="col-lg-6 d-none d-lg-flex brand-side align-items-center justify-content-center p-5"
    >

        <div style="max-width:520px; z-index:1;">

            <div class="mb-5">

                <h2 class="fw-bold mb-4">
                    Gestão Integrada de Programas FIC,
                    Bolsistas e Execução Administrativa
                </h2>

                <p
                    class="lead"
                    style="opacity:.85;"
                >
                    Plataforma institucional para controle
                    operacional de bolsas, frequências,
                    pagamentos e acompanhamento de programas
                    educacionais.
                </p>

            </div>

            <div class="feature-item">

                <h6>
                    🔐 Acesso Institucional Integrado
                </h6>

                <p>
                    Autenticação centralizada com contas institucionais
                    e controle de permissões por perfil.
                </p>

            </div>

            <div class="feature-item">

                <h6>
                    📋 Frequências e Execução Operacional
                </h6>

                <p>
                    Registro, validação e acompanhamento das atividades
                    vinculadas aos programas e bolsas.
                </p>

            </div>

            <div class="feature-item">

                <h6>
                    💰 Gestão de Bolsas e Pagamentos
                </h6>

                <p>
                    Controle administrativo de pagamentos,
                    recibos e acompanhamento financeiro.
                </p>

            </div>

        </div>

    </div>

</div>

@endsection