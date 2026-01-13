<div class="modal fade" id="bulkRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="bulk-reject-form" method="POST" action="{{ route('admin.homologations.bulk') }}">
            @csrf
            <input type="hidden" name="action" value="reject">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Recusar Registros Selecionados</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="reason" class="form-control" placeholder="Informe o motivo da recusa" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Confirmar Recusa</button>
                </div>
            </div>
        </form>
    </div>
</div>
