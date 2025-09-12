{{-- resources/views/components/sidebar.blade.php (Versão Melhorada) --}}
<aside class="w-64 bg-gray-800 text-gray-200 flex-shrink-0 p-4 flex flex-col">
    <div class="flex items-center justify-center h-16 border-b border-gray-700 flex-shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="text-xl font-semibold tracking-widest text-white uppercase">
            BolsistaControl
        </a>
    </div>

    <nav class="mt-6 flex-1">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center px-2 py-2 text-sm font-medium rounded-md transition duration-150
                  {{ request()->routeIs('admin.dashboard') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700' }}">
            <i class="fas fa-tachometer-alt fa-fw mr-3"></i>
            Dashboard
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="flex items-center mt-1 px-2 py-2 text-sm font-medium rounded-md transition duration-150
                  {{ request()->routeIs('admin.users.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700' }}">
            <i class="fas fa-user-shield fa-fw mr-3"></i>
            Utilizadores
        </a>

        <a href="{{ route('admin.scholarship_holders.index') }}"
           class="flex items-center mt-1 px-2 py-2 text-sm font-medium rounded-md transition duration-150
                  {{ request()->routeIs('admin.scholarship_holders.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700' }}">
            <i class="fas fa-users fa-fw mr-3"></i>
            Bolsistas
        </a>

        <a href="{{ route('admin.units.index') }}"
           class="flex items-center mt-1 px-2 py-2 text-sm font-medium rounded-md transition duration-150
                  {{ request()->routeIs('admin.units.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700' }}">
            <i class="fas fa-building fa-fw mr-3"></i>
            Unidades
        </a>

        <a href="{{ route('admin.reports.index') }}"
           class="flex items-center mt-1 px-2 py-2 text-sm font-medium rounded-md transition duration-150
                  {{ request()->routeIs('admin.reports.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700' }}">
            <i class="fas fa-chart-bar fa-fw mr-3"></i>
            Relatórios
        </a>
    </nav>
</aside>