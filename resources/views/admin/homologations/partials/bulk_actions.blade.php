<div class="mb-3">
    <div class="btn-group" role="group">
        {{-- Homologar em lote --}}
        <button type="button" id="bulk-approve" class="btn btn-success">
            <i class="bi bi-check-circle"></i> Homologar Selecionados
        </button>

        {{-- Rejeitar em lote --}}
        <button type="button" id="bulk-reject" class="btn btn-danger">
            <i class="bi bi-x-circle"></i> Rejeitar Selecionados
        </button>
    </div>
</div>

{{-- Modal para rejeição em lote --}}
<div class="modal fade" id="bulkRejectModal" tabindex="-1" aria-labelledby="bulkRejectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="bulkRejectForm">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="bulkRejectModalLabel">Rejeitar Registros Selecionados</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label for="bulk-reason" class="form-label">Motivo da rejeição</label>
                <textarea name="reason" id="bulk-reason" class="form-control sgb-textarea" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger">Rejeitar</button>
          </div>
        </div>
    </form>
  </div>
</div>
