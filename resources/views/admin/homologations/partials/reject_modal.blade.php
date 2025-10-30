<div class="modal fade" id="rejectModal-{{ $record->id }}" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('scholarship_holder.homologations.reject', $record->id) }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Motivo da Rejeição</h5>
        </div>
        <div class="modal-body">
          <textarea name="rejection_reason" class="form-control" required></textarea>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Confirmar Rejeição</button>
        </div>
      </div>
    </form>
  </div>
</div>
