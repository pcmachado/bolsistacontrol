<nav class="nav flex-column">
  <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
  {{-- exemplo de links --}}
  <a class="nav-link" href="#">Bolsistas</a>
  <a class="nav-link" href="#">Atendimentos</a>
  <a class="nav-link" href="#">Relatórios</a>
</nav>
