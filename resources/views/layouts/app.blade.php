<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'ProBolsas - Portal de Gestão de Bolsas'))</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-light">

<div id="wrapper" class="d-flex">

    {{-- SIDEBAR DESKTOP --}}
    @include('layouts.partials._sidebar')

    {{-- ÁREA PRINCIPAL --}}
    <div id="main-content" class="flex-grow-1 d-flex flex-column">
        @if(session('impersonated_by'))
        <div class="bg-warning text-dark text-center py-2">
            <strong>⚠ Você está logado como outro usuário</strong>

            <form method="POST"
                action="{{ route('admin.impersonate.stop') }}"
                class="d-inline ms-3">
                @csrf
                <button class="btn btn-dark btn-sm">
                    🔙 Voltar para sua conta
                </button>
            </form>
        </div>
        @endif

        {{-- NAVBAR --}}
        @include('layouts.partials._navbar')

        @if(session('error'))
            <div class="alert alert-danger mb-0 rounded-0">
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning mb-0 rounded-0">
                {{ session('warning') }}
            </div>
        @endif

        @auth
            @if(! session('warning') && auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="alert alert-warning d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-0 rounded-0">
                    <div>
                        <strong>Aviso:</strong> seu e-mail ainda não foi verificado. O acesso está liberado, mas confirme seu endereço quando possível.
                    </div>

                    <form method="POST" action="{{ route('verification.send') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-dark">
                            Reenviar verificação
                        </button>
                    </form>
                </div>
            @endif
        @endauth

        {{-- SIDEBAR MOBILE --}}
        <div class="offcanvas offcanvas-start mobile-sidebar-offcanvas" id="sidebarOffcanvas">
            <div class="offcanvas-header">
                <h5 class="text-white">Menu</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body p-0">
                @include('layouts.partials._sidebar_mobile')
            </div>
        </div>

        {{-- CONTEÚDO PRINCIPAL --}}
        <main id="content-area" class="flex-grow-1 p-4">
            @yield('content')
        </main>

        {{-- FOOTER --}}
        <footer id="app-footer" class="bg-white border-top py-3 text-center text-muted small">
            2026 — ProBolsas - Sistema de Gestão de Bolsas, Frequência e Pagamentos Acadêmicos - v1.0
        </footer>

    </div>
</div>

{{-- TOGGLE SIDEBAR --}}
<script>
document.addEventListener("DOMContentLoaded", () => {

    const wrapper = document.getElementById("wrapper");
    const sidebarMobile = document.getElementById("sidebarOffcanvas");
    const offcanvasInstance = bootstrap.Offcanvas.getOrCreateInstance(sidebarMobile);

    // Toggle Sidebar (desktop + mobile)
    document.querySelectorAll("[data-sidebar-toggle]").forEach(btn => {
        btn.addEventListener("click", () => {
            if (window.innerWidth >= 992) {
                wrapper.classList.toggle("sidebar-collapsed");
            } else {
                offcanvasInstance.toggle();
            }
        });
    });

    // Toggle Submenus
    document.querySelectorAll(".sidebar-submenu-toggle").forEach(btn => {
        btn.addEventListener("click", () => {
            btn.parentElement.classList.toggle("open");
        });
    });

});
</script>

@stack('scripts')

</body>
</html>
