import './bootstrap';
import $ from 'jquery';
window.$ = window.jQuery = $;

// DataTables + Bootstrap 5
import 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';

document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const body = document.body;

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            body.classList.toggle('sidebar-collapsed');
        });
    }

    // Inicia fechado no mobile
    if (window.innerWidth < 992) {
        sidebar.classList.add('collapsed');
        body.classList.add('sidebar-collapsed');
    }
});
