<aside class="bg-gray-800 text-white w-64 flex-shrink-0">
    <div class="p-4 text-xl font-bold border-b border-gray-700">
        {{ config('app.name', 'BolsistaControl') }}
    </div>
    <nav class="p-4 space-y-2">
        <a href="{{ route('admin.dashboard') }}"
           class="block px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-900' : '' }}">
            Dashboard
        </a>
        <a href="{{ route('admin.usuarios') }}"
           class="block px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.usuarios') ? 'bg-gray-900' : '' }}">
            Usuários
        </a>
        {{-- Adicione outros links aqui --}}
    </nav>
</aside>
