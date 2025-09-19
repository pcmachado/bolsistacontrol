import './bootstrap';
import $ from 'jquery';

// DataTables núcleo + tema Tailwind
import DataTable from 'datatables.net';
import 'datatables.net-responsive-dt';

window.$ = window.jQuery = $;
// Disponibiliza globalmente
window.DataTable = DataTable;