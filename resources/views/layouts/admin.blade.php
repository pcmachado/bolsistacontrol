<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Sistema de Bolsistas')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar { min-height: 100vh; background-color: #343a40; color: #fff; }
        .sidebar a { color: #ccc; }
        .sidebar a:hover { color: #fff; }
        .content { margin-left: 200px; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar da Área Administrativa -->
        <div class="sidebar position-fixed top-0 bottom-0 left-0 p-3" style="width: 200px;">
            <h4 class="text-white">Admin Dashboard</h4>
            <hr class="text-white">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.cargos.index') }}">
                        <i class="fas fa-briefcase me-2"></i> Gerenciar Cargos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('bolsistas.index') }}">
                        <i class="fas fa-user-friends me-2"></i> Gerenciar Bolsistas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('unidades.index') }}">
                        <i class="fas fa-building me-2"></i> Gerenciar Unidades
                    </a>
                </li>
            </ul>
            <hr class="text-white">
            <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-arrow-left me-2"></i> Voltar</a>
        </div>

        <!-- Conteúdo Principal -->
        <div class="content flex-grow-1 p-4">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>