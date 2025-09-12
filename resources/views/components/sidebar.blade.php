{{-- resources/views/components/sidebar.blade.php (Elegante e à Direita) --}}
<aside class="w-64 bg-gray-800 text-gray-200 flex-shrink-0 p-4 hidden md:block">
    <div class="flex items-center justify-center h-16 border-b border-gray-700">
        <a href="{{ route('dashboard') }}" class="text-xl font-semibold tracking-widest text-white uppercase">
            Bolsista Control
        </a>
    </div>

    <nav class="mt-6">
        {{-- Links do Bolsista --}}
        <div>
            <h3 class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Bolsista</h3>
            <div class="mt-2">
                <a href="{{ route('dashboard') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md transition duration-150
                    {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700' }}">
                    <i class="fas fa-tachometer-alt fa-fw mr-3"></i>
                    Dashboard
                </a>
                <a href="#" class="flex items-center mt-1 px-2 py-2 text-sm font-medium rounded-md transition duration-150 hover:bg-gray-700">
                     <i class="fas fa-tasks fa-fw mr-3"></i>
                    Minhas Frequências
                </a>
            </div>
        </div>

        {{-- Links do Admin --}}
        @if(auth()->user()->hasRole('admin|coordenador_geral|coordenador_adjunto'))
            <div class="mt-8">
                <h3 class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Admin</h3>
                <div class="mt-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md transition duration-150
                        {{ request()->routeIs('admin.dashboard') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-cogs fa-fw mr-3"></i>
                        Dashboard Admin
                    </a>
                    <a href="{{ route('admin.scholarship-holders.index') }}" class="flex items-center mt-1 px-2 py-2 text-sm font-medium rounded-md transition duration-150
                        {{ request()->routeIs('admin.scholarship-holders.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-users fa-fw mr-3"></i>
                        Bolsistas
                    </a>
                    <a href="{{ route('admin.units.index') }}" class="flex items-center mt-1 px-2 py-2 text-sm font-medium rounded-md transition duration-150
                        {{ request()->routeIs('admin.units.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-building fa-fw mr-3"></i>
                        Unidades
                    </a>
                     <a href="{{ route('admin.positions.index') }}" class="flex items-center mt-1 px-2 py-2 text-sm font-medium rounded-md transition duration-150
                        {{ request()->routeIs('admin.positions.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-briefcase fa-fw mr-3"></i>
                        Cargos
                    </a>
                     <a href="{{ route('admin.users.index') }}" class="flex items-center mt-1 px-2 py-2 text-sm font-medium rounded-md transition duration-150
                        {{ request()->routeIs('admin.users.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700' }}">
                        <i class="fas fa-user-shield fa-fw mr-3"></i>
                        Utilizadores
                    </a>
                </div>
            </div>
        @endif
    </nav>
</aside>