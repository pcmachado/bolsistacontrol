<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('home') }}">BolsistaControl</a>

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
