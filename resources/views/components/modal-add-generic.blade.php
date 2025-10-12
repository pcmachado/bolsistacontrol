<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ $route }}" id="{{ $id }}Form">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $title }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              @foreach($fields as $field)
                  <div class="mb-3">
                      <label class="form-label">{{ $field['label'] }}</label>
                      <input 
                          type="{{ $field['type'] ?? 'text' }}" 
                          name="{{ $field['name'] }}" 
                          class="form-control"
                          {{ $field['required'] ?? false ? 'required' : '' }}
                      >
                  </div>
              @endforeach
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Salvar</button>
          </div>
        </div>
    </form>
  </div>
</div>
