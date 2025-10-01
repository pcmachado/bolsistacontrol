<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- Bootstrap CSS --}}
        <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">

        {{-- Bootstrap Icons --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- Dependências para Excel e PDF -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

        @push('styles')
            <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
            <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
        @endpush

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
<body class="antialiased">
    @auth
        <div class="d-flex w-100" id="app">
        <x-sidebar />

            <div class="content-wrapper flex-grow-1">
                <x-navbar />
                
                <main class="main-content">
                    @yield('content')
                </main>
                
                <!-- Footer Simples -->
                <footer class="bg-white border-top p-3 text-center text-muted small mt-auto">
                    2025 by Paulo César Machado. Todos os direitos reservados.
                </footer>
            </div>
        </div>
        
    @else
        <!-- Seção para usuários não autenticados (Ex: tela de login/registro) -->
        <main class="flex-grow-1 p-4 w-100">
            @yield('content')
        </main>
    @endauth

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
