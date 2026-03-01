<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ProBolsas - Portal de Gestão de Bolsas')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
</head>

<body class="bg-light d-flex flex-column min-vh-100">

    {{-- Navbar visitante --}}
    <nav class="navbar navbar-light bg-white border-bottom">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                🎓 ProBolsas
            </a>

            <div>
                <a href="{{ route('contact') }}" class="btn btn-outline-secondary">Contato</a>
            </div>
        </div>
    </nav>

    {{-- Conteúdo --}}
    <main class="flex-fill">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-top py-3 text-center text-muted small">
        © 2025 — Paulo César Machado. Todos os direitos reservados.
    </footer>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</body>
</html>
