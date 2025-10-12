import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'alpinejs';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import $ from 'jquery';
window.$ = $;
window.jQuery = $;

// Se quiser DataTables via npm (senão mantém via CDN)
import 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5.min.js';
import 'datatables.net-buttons/js/buttons.print.min.js';

// Se quiser Select2 via npm
import 'select2';

// Importa seu JS customizado
import { handleAjaxForm } from './modal-ajax';

// Exemplo de uso da função handleAjaxForm
handleAjaxForm('addInstitutionForm', 'institution_id', 'addInstitutionModal');
handleAjaxForm('addCourseForm', 'course_id', 'addCourseModal');
handleAjaxForm('addProjectForm', 'project_id', 'addProjectModal');
handleAjaxForm('addScholarshipHolderForm', 'scholarship_holder_id', 'addScholarshipHolderModal');

