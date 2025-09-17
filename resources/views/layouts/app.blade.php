<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bolsista Control')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    {{-- Vite para assets --}}

    @vite(['resources/css/app.css'])
</head>
<body>
    {{-- Navbar como componente --}}
    <x-navbar />

    <div class="d-flex" style="padding-top: 56px;">
        {{-- Sidebar --}}
        @auth
            @include('components.sidebar')
        @endauth

        {{-- Conteúdo principal --}}
        <div class="flex-grow-1 p-4" style="min-height:100vh; overflow-y:auto;">
            @yield('content')
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

    {{-- Vite para assets --}}  
    @vite(['resources/js/app.js'])
    @stack('scripts')

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const html = document.documentElement;
        const themeToggle = document.getElementById("themeToggle");

        if (!themeToggle) return;

        const storedTheme = localStorage.getItem("theme") || "light";
        html.setAttribute("data-bs-theme", storedTheme);
        updateButtonIcon(storedTheme);

        themeToggle.addEventListener("click", () => {
          const currentTheme = html.getAttribute("data-bs-theme");
          const newTheme = currentTheme === "light" ? "dark" : "light";

          html.setAttribute("data-bs-theme", newTheme);
          localStorage.setItem("theme", newTheme);
          updateButtonIcon(newTheme);
        });

        function updateButtonIcon(theme) {
          if (theme === "dark") {
            themeToggle.innerHTML = '<i class="bi bi-sun"></i>';
            themeToggle.classList.remove("btn-outline-light");
            themeToggle.classList.add("btn-outline-warning");
          } else {
            themeToggle.innerHTML = '<i class="bi bi-moon"></i>';
            themeToggle.classList.remove("btn-outline-warning");
            themeToggle.classList.add("btn-outline-dark");
          }
        }
      });
    </script>
</body>
</html>
