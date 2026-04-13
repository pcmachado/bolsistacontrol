// resources/js/homologations.js

jQuery(function ($) {
    const table = window.LaravelDataTables?.['homologations-table']
        ?? ($('#homologations-table').length ? $('#homologations-table').DataTable() : null);

    if (!table) {
        return;
    }

    $('#select-all').on('click', function () {
        $('input[name="submissions[]"]').prop('checked', this.checked);
    });

    function getSelectedSubmissions() {
        const ids = [];

        $('input[name="submissions[]"]:checked').each(function () {
            ids.push($(this).val());
        });

        return ids;
    }

    function postBulk(action, reason = null) {
        const submissions = getSelectedSubmissions();

        if (submissions.length === 0) {
            alert('Selecione pelo menos uma submissao.');
            return;
        }

        const payload = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            action,
            submissions,
        };

        if (reason) {
            payload.reason = reason;
        }

        $.ajax({
            url: '/admin/homologations/bulk',
            method: 'POST',
            data: payload,
            success: function (resp) {
                alert(resp.message || 'Operacao realizada com sucesso.');
                table.ajax.reload();
            },
            error: function () {
                alert('Erro ao processar submissoes.');
            },
        });
    }

    $('#bulk-approve').on('click', function () {
        postBulk('approve');
    });

    $('#bulk-reject').on('click', function () {
        const ids = getSelectedSubmissions();

        if (ids.length === 0) {
            alert('Selecione pelo menos uma submissao.');
            return;
        }

        $('#bulkRejectModal').modal('show');
    });

    $('#bulkRejectForm, #bulk-reject-form').on('submit', function (e) {
        e.preventDefault();

        const reason = $('#bulk-reason').val() || $(this).find('textarea[name="reason"]').val();

        if (!reason) {
            alert('Informe o motivo da rejeicao.');
            return;
        }

        postBulk('reject', reason);
        $('#bulkRejectModal').modal('hide');
    });
});
