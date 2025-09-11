<nav class="bg-white w-64 min-h-screen p-4 border-r">
    <ul class="space-y-2">
        <li><a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-gray-100">Dashboard</a></li>
        <li><a href="{{ route('admin.usuarios') }}" class="block px-4 py-2 hover:bg-gray-100">Usuários</a></li>
        <!--<li><a href="{ route('admin.configuracoes') }" class="block px-4 py-2 hover:bg-gray-100">Configurações</a></li>-->
        <li><a href="{{ route('admin.relatorios.index') }}" class="block px-4 py-2 hover:bg-gray-100">Relatórios</a></li>
        <li><a href="{{ route('admin.bolsistas.index') }}" class="block px-4 py-2 hover:bg-gray-100">Bolsistas</a></li>
        <li><a href="{{ route('admin.unidades.index') }}" class="block px-4 py-2 hover:bg-gray-100">Unidades</a></li>
        <!--<li><a href="{ route('admin.permissoes') }" class="block px-4 py-2 hover:bg-gray-100">Permissões</a></li>-->
        <!--<li><a href="{ route('admin.logs') }" class="block px-4 py-2 hover:bg-gray-100">Logs</a></li>-->
    </ul>
</nav>