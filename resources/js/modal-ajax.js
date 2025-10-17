export function handleAjaxForm(formId, selectName, modalId) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            const select = document.querySelector(`select[name="${selectName}"]`);
            if (select) {
                const option = new Option(data.name, data.id, true, true);
                select.add(option);
            }

            const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
            modal.hide();

            form.reset();
        })
        .catch(err => console.error('Erro ao salvar:', err));
    });
}

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    handleAjaxForm('addPositionModalForm', 'positions[]', 'addPositionModal');
});
