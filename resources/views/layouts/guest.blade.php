<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
</head>
<body class="bg-light page-{{ str_replace('.', '-', Route::currentRouteName()) }}">
    <div class="d-flex" id="wrapper">

        <!-- Conteúdo da Página -->
        <div id="page-content-wrapper">

            {{-- Navbar simplificada para visitantes --}}
            <nav class="navbar navbar-light bg-white border-bottom">
                <div class="container d-flex justify-content-between align-items-center">
                    <a class="navbar-brand fw-bold" href="{{ url('/') }}">🎓 BolsistaControl</a>
                    
                    <div>
                        <a href="{{ route('login') }}" class="btn btn-primary me-2">Entrar</a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-secondary">Contato</a>
                    </div>
                </div>
            </nav>

            <!-- Conteúdo Principal -->
            <main class="container-fluid p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-top p-3 text-center text-muted small mt-auto">
        2025 by Paulo César Machado. Todos os direitos reservados.
    </footer>

    <!-- Sidebar Toggle -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const wrapper = document.getElementById('wrapper');
            const offcanvasSidebar = new bootstrap.Offcanvas('#sidebarOffcanvas');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (window.innerWidth >= 992) {
                        // Desktop → alterna sidebar fixa
                        wrapper.classList.toggle('collapsed');
                    } else {
                        // Mobile → abre o offcanvas
                        offcanvasSidebar.toggle();
                    }
                });
            }

            // Reaplica tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>

    <!-- Tooltips -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
 @vite(['resources/css/app.css', 'resources/js/app.js'])
 @stack('scripts')
</body>
</html>
