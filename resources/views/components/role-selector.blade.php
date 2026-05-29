{{-- Componente para selecao de roles com hierarquia --}}
@props([
    'selectedRoles' => [],
    'user' => null,
    'multiple' => true,
    'required' => false,
    'name' => 'role'
])

@php
    $currentUser = $user ?? auth()->user();
    $templates = \App\Support\PermissionRegistry::roleTemplates();
    $assignableRoleNames = $currentUser
        ? \App\Support\RoleAccess::assignableRoleNames($currentUser)
        : collect();

    $colors = [
        'superadmin' => 'danger',
        'admin' => 'warning',
        'coordenador_geral' => 'info',
        'coordenador_adjunto_geral' => 'primary',
        'coordenador_adjunto' => 'secondary',
        'professor' => 'primary',
        'apoio_administrativo' => 'info',
        'supervisor' => 'secondary',
        'orientador' => 'secondary',
        'bolsista' => 'success',
    ];

    $availableRoles = collect($templates)
        ->only($assignableRoleNames)
        ->map(fn ($config, $roleKey) => [
            'name' => $config['label'],
            'weight' => $config['level'],
            'color' => $colors[$roleKey] ?? 'secondary',
            'description' => $config['label'],
        ]);
@endphp

<div class="form-group">
    <label for="roles-selector" class="form-label">
        <strong>{{ $multiple ? 'Funcoes' : 'Funcao' }}</strong>
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    @if($multiple)
        <select
            name="{{ $name }}[]"
            id="roles-selector"
            class="form-control"
            multiple
            {{ $required ? 'required' : '' }}
            style="min-height: 120px;"
        >
            @foreach($availableRoles as $roleKey => $config)
                <option
                    value="{{ $roleKey }}"
                    {{ in_array($roleKey, $selectedRoles) ? 'selected' : '' }}
                    data-weight="{{ $config['weight'] }}"
                    data-description="{{ $config['description'] }}"
                    class="role-option-{{ $config['color'] }}"
                >
                    {{ $config['name'] }} ({{ $config['weight'] }})
                </option>
            @endforeach
        </select>
    @else
        <select
            name="{{ $name }}"
            id="roles-selector"
            class="form-control"
            {{ $required ? 'required' : '' }}
        >
            <option value="">Selecione uma funcao</option>
            @foreach($availableRoles as $roleKey => $config)
                <option
                    value="{{ $roleKey }}"
                    {{ in_array($roleKey, $selectedRoles) ? 'selected' : '' }}
                    data-weight="{{ $config['weight'] }}"
                    data-description="{{ $config['description'] }}"
                >
                    {{ $config['name'] }}
                </option>
            @endforeach
        </select>
    @endif

    <small class="form-text text-muted">
        @if($multiple)
            Selecione uma ou mais funcoes. Cada funcao tem um nivel de permissao diferente.
        @else
            Selecione a funcao principal do usuario.
        @endif
    </small>

    @if($multiple)
        <div id="selected-roles-info" class="mt-2" style="display: none;">
            <small class="text-info">
                <i class="fas fa-info-circle"></i>
                <span id="role-description"></span>
            </small>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelector = document.getElementById('roles-selector');

    if (! roleSelector) {
        return;
    }

    @if($multiple)
        roleSelector.addEventListener('change', function() {
            const selectedOptions = Array.from(this.selectedOptions);
            const infoDiv = document.getElementById('selected-roles-info');
            const descriptionSpan = document.getElementById('role-description');

            if (selectedOptions.length > 0) {
                const lastSelected = selectedOptions[selectedOptions.length - 1];
                descriptionSpan.textContent = lastSelected.getAttribute('data-description');
                infoDiv.style.display = 'block';
            } else {
                infoDiv.style.display = 'none';
            }
        });
    @else
        roleSelector.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const description = selectedOption.getAttribute('data-description');
            this.title = description && this.value ? description : '';
        });
    @endif
});
</script>
@endpush

<style>
.role-option-danger { color: #dc3545; }
.role-option-warning { color: #ffc107; }
.role-option-info { color: #0dcaf0; }
.role-option-primary { color: #0d6efd; }
.role-option-secondary { color: #6c757d; }
.role-option-success { color: #198754; }
</style>
