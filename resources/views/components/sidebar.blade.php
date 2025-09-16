<div class="sidebar p-3 vh-100" style="width: 220px;">
    <h4 class="text-center mb-4">Menu</h4>
    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                Usuários
            </a>
        </li>
        {{-- Adicione outros links aqui --}}
    </ul>
</div>
