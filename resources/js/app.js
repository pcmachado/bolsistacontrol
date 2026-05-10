/**
 * app.js — versão estável com ordem correta
 */
// === jQuery ===
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

// === Bootstrap (módulo único) ===
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// === DataTables ===
import 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5.min.js';
import 'datatables.net-buttons/js/buttons.print.min.js';

// === Select2 ===
import 'select2';

import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.bootstrap5.css";

window.TomSelect = TomSelect;

// === Outros utilitários ===
import 'alpinejs';

// === Funções personalizadas ===
import { handleAjaxForm } from './modal-ajax';

// === TinyMCE ===
import tinymce from 'tinymce';
import 'tinymce/themes/silver';
import 'tinymce/icons/default';
import 'tinymce/models/dom';

// === Inicializações globais (executa após DOM carregado) ===
document.addEventListener('DOMContentLoaded', async () => {
    //console.log('Bootstrap:', typeof bootstrap);
    //console.log('jQuery:', typeof jQuery);

    // Tooltips e dropdowns Bootstrap
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
        bootstrap.Dropdown.getOrCreateInstance(el);
    });
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        bootstrap.Tooltip.getOrCreateInstance(el);
    });

    // Executa formulários AJAX
    handleAjaxForm('addInstitutionForm', 'institution_id', 'addInstitutionModal');
    handleAjaxForm('addCourseForm', 'course_id', 'addCourseModal');
    handleAjaxForm('addProjectForm', 'project_id', 'addProjectModal');
    handleAjaxForm('addScholarshipHolderForm', 'scholarship_holder_id', 'addScholarshipHolderModal');

    // === Carrega o script específico da página (ex: homologations) ===
    // Importa de forma assíncrona só quando o DOM e jQuery já existem
    if (document.body.classList.contains('page-admin-homologations-index')) {
        await import('./homologations');
    }
});

window.addEventListener('DOMContentLoaded', () => {
    const editors = document.querySelectorAll('textarea[data-editor="tinymce"]');

    if (!editors.length) return;

    tinymce.init({
        selector: '#sgb-tinymce', // ID do seu campo
        license_key: 'gpl',        // Necessário nas versões mais novas para uso local
        base_url: '/js/tinymce',   // Caminho onde o Vite copiou os arquivos
        suffix: '.min',
        height: 300,
        menubar: false,
        plugins: 'lists link',
        toolbar: 'bold italic | bullist numlist | link',
    });
});
