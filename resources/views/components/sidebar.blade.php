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
        <li class="nav-item mb-2">
            <a class="nav-link {{ request()->routeIs('admin.units.*') ? 'active' : '' }}" href="{{ route('admin.units.index') }}">
                Unidades
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link {{ request()->routeIs('admin.scholarship_holders.*') ? 'active' : '' }}" href="{{ route('admin.scholarship_holders.index') }}">
                Bolsistas
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                Relatórios
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">
                Frequências
            </a>
        </li>
    </ul>
</div>
