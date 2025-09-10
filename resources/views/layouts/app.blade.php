<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name', 'BolsistaControl') }}</title>

    {{-- Estilos compilados pelo Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Espaço para estilos adicionais --}}
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col md:flex-row">
        @include('components.sidebar')
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>