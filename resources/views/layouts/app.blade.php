<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'BolsistaControl'))</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    {{-- Editor (opcional) --}}
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">

<div id="wrapper" class="d-flex">

    {{-- SIDEBAR DESKTOP --}}
    @include('layouts.partials._sidebar')

    {{-- ÁREA PRINCIPAL --}}
    <div id="main-content" class="flex-grow-1 d-flex flex-column">

        {{-- NAVBAR --}}
        @include('layouts.partials._navbar')

        {{-- SIDEBAR MOBILE --}}
        <div class="offcanvas offcanvas-start bg-dark text-white" id="sidebarOffcanvas">
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
            2025 — BolsistaControl © Todos os direitos reservados.
        </footer>

    </div>
</div>

{{-- TOGGLE SIDEBAR --}}
<script>
document.addEventListener("DOMContentLoaded", () => {

    const wrapper = document.getElementById("wrapper");
    const toggles = document.querySelectorAll("[data-sidebar-toggle]");
    const mobileSidebar = bootstrap.Offcanvas ? new bootstrap.Offcanvas("#sidebarOffcanvas") : null;

    /* --- COLAPSO LATERAL --- */
    toggles.forEach(btn => {
        btn.addEventListener("click", () => {
            if (window.innerWidth >= 992) {
                wrapper.classList.toggle("sidebar-collapsed");
            } else if (mobileSidebar) {
                mobileSidebar.toggle();
            }
        });
    });


    /* --- SUBMENUS DO SIDEBAR --- */
    document.querySelectorAll(".sidebar-submenu-toggle").forEach(toggle => {
        toggle.addEventListener("click", () => {

            const submenuKey = toggle.dataset.submenu;
            const submenu = document.getElementById(`submenu-${submenuKey}`);

            if (!submenu) return;

            submenu.classList.toggle("open");

            // Girar seta
            const arrow = toggle.querySelector(".arrow-icon");
            if (arrow) arrow.classList.toggle("rotate-180");
        });
    });

});
</script>

{{-- DARK/LIGHT THEME --}}
<script>
(function () {
    const KEY = 'bolsistacontrol_theme';

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
    }

    function toggleTheme() {
        const current = localStorage.getItem(KEY) || 'light';
        const next = current === 'dark' ? 'light' : 'light';
        localStorage.setItem(KEY, next);
        applyTheme(next);
    }

    document.addEventListener("DOMContentLoaded", () => {
        applyTheme(localStorage.getItem(KEY) || 'light');

        const themeButton = document.getElementById("themeToggle");
        if (themeButton) themeButton.addEventListener("click", toggleTheme);
    });
})();
</script>

@stack('scripts')

</body>
</html>
