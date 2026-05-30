<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ProBolsas - Portal de Gestão Administrativa de Bolsas')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">

    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap"
        rel="stylesheet"
    />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --probolsas-blue: #032454;
            --probolsas-green: #49b35d;
            --probolsas-light: #f4f7fb;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--probolsas-light);
        }

        .navbar-probolsas {
            background: var(--probolsas-blue);
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        .navbar-brand img {
            height: 56px;
            width: auto;
        }

        .footer-probolsas {
            background: #ffffff;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-dark navbar-probolsas py-3">
        <div class="container">

            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img
                    src="{{ asset('images/probolsas_fundo_escuro.png') }}"
                    alt="ProBolsas"
                >
            </a>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('login') }}"
                   class="btn btn-outline-light rounded-pill px-4">
                    Entrar
                </a>
            </div>

        </div>
    </nav>

    {{-- Conteúdo --}}
    <main class="flex-fill">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="footer-probolsas py-3 text-center text-muted small">
        {{ date('Y') }} — ProBolsas - Sistema Administrativo de Gestão de Bolsas, Frequências e Pagamentos · {{ $currentVersion }}
    </footer>

    @stack('scripts')

</body>
</html>