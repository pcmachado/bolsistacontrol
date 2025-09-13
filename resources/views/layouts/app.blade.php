<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Painel') - BolsistaControl</title>

    <!-- Bootstrap CSS (CDN para simplicidade) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body { padding-top: 56px; }
      .sidebar { min-height: 100vh; }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">BolsistaControl</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        @auth
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            {{ auth()->user()->name }}
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button class="dropdown-item">Sair</button>
              </form>
            </li>
          </ul>
        </li>
        @else
        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
        @endauth
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
    @auth
      <aside class="col-md-2 bg-light sidebar p-3">
        @include('components.sidebar')
      </aside>
      <main class="col-md-10 py-4">
        @yield('content')
      </main>
    @else
      <main class="col-12 py-4">
        @yield('content')
      </main>
    @endauth
  </div>
</div>

<!-- Bootstrap JS (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
