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

        {{-- NAVBAR --}}
        @include('layouts.partials._navbar')

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

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
