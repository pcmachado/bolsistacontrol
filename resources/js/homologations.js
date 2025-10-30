// resources/js/homologations.js

jQuery(function($) {
    let table = $('#homologations-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/admin/homologations',
            data: function (d) {
                d.unit_id = $('#unit_id').val();
                d.scholarship_holder_id = $('#scholarship_holder_id').val();
                d.month = $('#month').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false },
            { data: 'date', name: 'date' },
            { data: 'scholarship_holder', name: 'scholarshipHolder.user.name' },
            { data: 'unit', name: 'scholarshipHolder.unit.name' },
            { data: 'hours', name: 'hours' },
            { data: 'status_label', name: 'status' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    // Botão aplicar filtros
    $('#applyFilters').on('click', function() {
        table.ajax.reload();
    });

    // Botão limpar filtros
    $('#resetFilters').on('click', function() {
        $('#filters')[0].reset();
        table.ajax.reload();
    });

    // Selecionar todos
    $('#select-all').on('click', function(){
        $('input[name="records[]"]').prop('checked', this.checked);
    });

    // Função para coletar IDs selecionados
    function getSelectedRecords() {
        let ids = [];
        $('input[name="records[]"]:checked').each(function() {
            ids.push($(this).val());
        });
        return ids;
    }

    // Homologar em lote
    $('#bulk-approve').on('click', function() {
        let ids = getSelectedRecords();
        if (ids.length === 0) {
            alert('Selecione pelo menos um registro.');
            return;
        }

        $.ajax({
            url: '/admin/homologations/bulk',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                action: 'approve',
                records: ids
            },
            success: function(resp) {
                alert(resp.message || 'Registros homologados com sucesso.');
                table.ajax.reload();
            },
            error: function() {
                alert('Erro ao homologar registros.');
            }
        });
    });

    // Abrir modal de rejeição em lote
    $('#bulk-reject').on('click', function() {
        let ids = getSelectedRecords();
        if (ids.length === 0) {
            alert('Selecione pelo menos um registro.');
            return;
        }
        $('#bulkRejectModal').modal('show');
    });

    // Submeter rejeição em lote
    $('#bulkRejectForm').on('submit', function(e) {
        e.preventDefault();
        let ids = getSelectedRecords();
        let reason = $('#bulk-reason').val();

        $.ajax({
            url: '/admin/homologations/bulk',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                action: 'reject',
                records: ids,
                reason: reason
            },
            success: function(resp) {
                alert(resp.message || 'Registros rejeitados com sucesso.');
                $('#bulkRejectModal').modal('hide');
                table.ajax.reload();
            },
            error: function() {
                alert('Erro ao rejeitar registros.');
            }
        });
    });
});
