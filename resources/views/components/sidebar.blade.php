<div class="bg-light border-end sidebar p-3" style="width: 250px;">
  <h6 class="text-muted">Menu</h6>
  <ul class="nav flex-column">
    <li class="nav-item">
      <a href="{{ route('admin.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('admin.users.index') }}" class="nav-link">
        <i class="bi bi-people"></i> Usuários
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('admin.scholarship_holders.index') }}" class="nav-link">
        <i class="bi bi-mortarboard"></i> Bolsistas
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('admin.projects.index') }}" class="nav-link">
        <i class="bi bi-folder"></i> Projetos
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('attendance.index') }}" class="nav-link">
        <i class="bi bi-calendar-check"></i> Frequência
      </a>
    </li>
  </ul>
</div>
