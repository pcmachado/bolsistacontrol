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

        <!-- Bootstrap CSS (versão mais recente e estável) -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

            <!-- DataTables & Buttons Bootstrap 5 CSS -->
        <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">

        
        <!-- CSS para Select2 -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <!-- Estilos Personalizados Integrados -->
        <style>
            body {
                overflow-x: hidden;
            }
            #wrapper {
                display: flex;
                transition: all 0.3s ease;
            }
            #page-content-wrapper {
                width: 100%;
            }
            #sidebar-wrapper {
                width: 15rem; /* Largura expandida */
                transition: width 0.3s ease;
            }
            /* Estilos para o estado colapsado */
            #wrapper.collapsed #sidebar-wrapper {
                width: 4.5rem; /* Largura apenas para os ícones */
            }
            #wrapper.collapsed .sidebar-text,
            #wrapper.collapsed .user-profile .small,
            #wrapper.collapsed .sidebar-heading hr {
                display: none;
            }
            #wrapper.collapsed #sidebar-wrapper .list-group-item {
                text-align: center;
            }
            #wrapper.collapsed #sidebar-wrapper .list-group-item i {
                margin-right: 0 !important;
            }
        </style>
    @stack('styles')
    </head>
    <body class="bg-light">
        <div class="d-flex" id="wrapper">
            <!-- Sidebar -->
            @include('layouts.partials._sidebar')

            <!-- Conteúdo da Página -->
            <div id="page-content-wrapper">
                <!-- Navbar -->
                @include('layouts.partials._navbar')

                <!-- Conteúdo Principal -->
                <main class="container-fluid p-4">
                    @yield('content')
                </main>
                
                <!-- Footer Simples -->
                <footer class="bg-white border-top p-3 text-center text-muted small mt-auto">
                    2025 by Paulo César Machado. Todos os direitos reservados.
                </footer>
            </div>
        </div>
        <!-- SCRIPTS -->
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        
        <!-- Bootstrap JS Bundle -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- DataTables Core & Bootstrap 5 integration -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

        <!-- DataTables Buttons extension & dependencies -->
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- Script para o toggle da Sidebar -->
        <script>
            window.addEventListener('DOMContentLoaded', event => {
                const sidebarToggle = document.body.querySelector('#sidebarToggle');
                if (sidebarToggle) {
                    sidebarToggle.addEventListener('click', event => {
                        event.preventDefault();
                        document.body.querySelector('#wrapper').classList.toggle('collapsed');
                    });
                }
            });
        </script>
        @stack('scripts')
    </body>
</html>
