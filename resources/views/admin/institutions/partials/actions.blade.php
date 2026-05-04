<div class="btn-group" role="group">
    {{-- Visualizar --}}
    <a href="{{ route('admin.institutions.show', $institution->id) }}"
       class="btn btn-sm btn-primary"
       title="Visualizar">
        <i class="bi bi-eye"></i>
    </a>

    {{-- Homologar --}}
    <button type="button"
            class="btn btn-sm btn-success btn-approve"
            data-id="{{ $institution->id }}"
            title="Homologar">
        <i class="bi bi-check-circle"></i>
    </button>

    {{-- Rejeitar --}}
    <button type="button"
            class="btn btn-sm btn-danger btn-reject"
            data-id="{{ $institution->id }}"
            title="Rejeitar">
        <i class="bi bi-x-circle"></i>
    </button>
</div>
