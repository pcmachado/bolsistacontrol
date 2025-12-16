<?php

use Illuminate\Support\Facades\Route;
use App\DataTables\UsersDataTable;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\ScholarshipHolderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HomologationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\institutionController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\FundingSourceController;
use App\Http\Controllers\Admin\ProjectWizardController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\DisciplineController;
use App\Http\Controllers\MyPaymentController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentExecutionController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\Admin\PaymentDashboardController;
use App\Http\Controllers\Admin\DocumentTemplateController;
use App\Http\Controllers\Admin\FinancialReportController;
use App\Http\Controllers\Admin\ClassOfferingDisciplineController;
use App\Http\Controllers\Admin\ClassOfferingScholarshipHolderController;
use App\Http\Controllers\Admin\ClassSessionController;
use App\Http\Controllers\Admin\ClassSessionReportController;
use App\Http\Controllers\Admin\ClassOfferingSyllabusController;
use App\Http\Controllers\Admin\ClassOfferingDashboardController;
use App\Http\Controllers\Admin\GlobalDashboardController;
use App\Http\Controllers\Admin\TeacherDashboardController;
use App\Http\Controllers\Admin\DisciplineDashboardController;
use App\Http\Controllers\Admin\UnitDashboardController;
use App\Http\Controllers\Admin\IntelligentAlertSettingController;
use App\Http\Controllers\Admin\SupervisorAssignmentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::resource('/dashboard', DashboardController::class)->only(['index'])->names(['index' => 'dashboard']);

    // Módulo de Frequência para o Bolsista
    // --- Minhas Frequências (sempre só os próprios registros) ---
    Route::get('/attendance/my', [AttendanceRecordController::class, 'index'])->name('attendance.my');
    Route::get('/attendance/create', [AttendanceRecordController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceRecordController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/history', [AttendanceRecordController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/pending', [AttendanceRecordController::class, 'pending'])->name('attendance.pending');
    Route::get('/attendance/card/approved', [AttendanceRecordController::class, 'approved'])->name('attendance.card.approved');
    Route::get('/attendance/card/submitted', [AttendanceRecordController::class, 'submitted'])->name('attendance.card.submitted');
    Route::get('/attendance/card/rejected', [AttendanceRecordController::class, 'rejected'])->name('attendance.card.rejected');
    Route::get('/attendance/card/late', [AttendanceRecordController::class, 'late'])->name('attendance.card.late');
    Route::get('/attendance/submissions', [AttendanceRecordController::class, 'submissions'])->name('attendance.submissions');
    Route::get('/attendance/approvals', [AttendanceRecordController::class, 'approvals'])->name('attendance.approvals');

    Route::get('/attendance/{attendanceRecord}/edit', [AttendanceRecordController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{attendanceRecord}', [AttendanceRecordController::class, 'update'])->name('attendance.update');
    Route::delete('/attendance/{attendanceRecord}', [AttendanceRecordController::class, 'destroy'])->name('attendance.destroy');
    Route::post('/attendance/{attendanceRecord}/submit', [AttendanceRecordController::class, 'submit'])->name('attendance.submit');
    Route::get('/attendance/{attendanceRecord}', [AttendanceRecordController::class, 'show'])->name('attendance.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/reports/my-report', [ReportController::class, 'individualReport'])->name('reports.myReport');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all', [NotificationController::class, 'markAll'])->name('notifications.markAll');
    
    Route::get('/notifications/read/{id}', [NotificationController::class, 'read'])->name('notifications.read');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');

    Route::middleware(['auth'])->group(function () {
        Route::get('/payments/my', [MyPaymentController::class, 'index'])->name('payments.my');
        Route::post('/payments/{payment}/confirm', [MyPaymentController::class, 'confirm'])->name('payments.confirm');
    });

    Route::get('/payments/{payment}/receipt', [PaymentReceiptController::class, 'download'])->name('payments.receipt');
    Route::get('/payments/{payment}/receipt', [MyPaymentController::class, 'receipt'])->name('payments.receipt');

});

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/scholarship_holders/search', [ScholarshipHolderController::class, 'search'])->name('scholarshipholders.search');
Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');

Route::middleware(['auth', 'verified', 'role_or_permission:Admin|coordenador_geral|coordenador_adjunto'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');

    Route::post('/{record}/approve', [HomologationController::class, 'approve'])->name('homologations.approve');
    Route::post('/{record}/reject', [HomologationController::class, 'reject'])->name('homologations.reject');

    // 🔹 Relatório de Homologações (apenas coordenador geral e adjunto)
    Route::get('/homologations/report', [HomologationController::class, 'report'])->name('homologations.report');
    Route::post('/homologations/bulk', [HomologationController::class, 'bulk'])->name('homologations.bulk');
    Route::get('/homologations/pending', [HomologationController::class, 'pending'])->name('homologations.pending');
    Route::get('/homologations', [HomologationController::class, 'index'])->name('homologations.index');
    Route::get('/homologations/late', [HomologationController::class, 'late'])->name('homologations.late');

    Route::get('/homologations/{id}', [HomologationController::class, 'show'])->name('homologations.show');

    // 🔹 Relatório Consolidado (apenas coordenador geral)
    Route::get('/reports/monthly', [ReportController::class, 'monthlyReport'])->name('reports.report');
    Route::get('/reports/pdf', [ReportController::class, 'reportPdf'])->name('reports.export_pdf');
    Route::get('/reports/excel', [ReportController::class, 'reportExcel'])->name('reports.export_excel');

    // 🔹 Relatório Detalhado por Unidade (coordenador geral)
    Route::get('/reports/unit/{unit?}/{project?}', [ReportController::class, 'unitDetail'])->name('reports.unit_detail');

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('units', UnitController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('scholarship_holders', ScholarshipHolderController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('notifications', NotificationController::class);
    Route::resource('attendance_records', AttendanceRecordController::class);
    Route::resource('institutions', InstitutionController::class);
    Route::resource('reports', ReportController::class);
    //Route::resource('homologations', HomologationController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('courses', CourseController::class);
    Route::resource('funding_sources', FundingSourceController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('assignments', AssignmentController::class);

    Route::resource('disciplines', DisciplineController::class);
    Route::resource('class-offerings', \App\Http\Controllers\Admin\ClassOfferingController::class);

    Route::get('/class-offerings/{offering}/disciplines',[ClassOfferingDisciplineController::class, 'index'])->name('class-offerings.disciplines');
    Route::post('/class-offerings/{offering}/disciplines',[ClassOfferingDisciplineController::class, 'store'])->name('class-offerings.disciplines.store');
    Route::put('/class-offerings/discipline/{pivot}',[ClassOfferingDisciplineController::class, 'update'])->name('class-offerings.disciplines.update');
    Route::delete('/class-offerings/discipline/{pivot}',[ClassOfferingDisciplineController::class, 'destroy'])->name('class-offerings.disciplines.destroy');

    Route::resource('supervisors', SupervisorAssignmentController::class);
    Route::resource('teachers', TeacherController::class);

    Route::get('/class-offerings/{offering}/scholarship_holders',[ClassOfferingScholarshipHolderController::class, 'index'])->name('class-offerings.scholarship_holders.index');

    Route::post('/class-offerings/{offering}/scholarship_holders',[ClassOfferingScholarshipHolderController::class, 'store'])->name('class-offerings.scholarship_holders.store');
    Route::delete('/class-offerings/{offering}/scholarship_holders/{scholarshipHolder}',[ClassOfferingScholarshipHolderController::class, 'destroy'])->name('class-offerings.scholarship_holders.destroy');

    Route::get('/class-offerings/{offering}/sessions',[ClassSessionController::class, 'index'])->name('class-offerings.sessions.index');

    Route::get('/class-offerings/{offering}/sessions/report',[ClassSessionReportController::class, 'index'])->name('class-offerings.sessions.report');
    Route::get('/reports/class-sessions',[ClassSessionReportController::class, 'global'])->name('reports.class-sessions');

    Route::get('/class-offerings/{offering}/syllabus',[ClassOfferingSyllabusController::class, 'index'])->name('class-offerings.syllabus');

    Route::get('/class-offerings/{offering}/dashboard',[ClassOfferingDashboardController::class, 'index'])->name('class-offerings.dashboard');

    Route::get('/class-offerings/{offering}/sessions/report/pdf',[ClassSessionReportController::class, 'exportPdf'])->name('class-offerings.sessions.report.pdf');
    Route::get('/class-offerings/{offering}/sessions/report/excel',[ClassSessionReportController::class, 'exportExcel'])->name('class-offerings.sessions.report.excel');

    Route::get('/dashboard/academic',[GlobalDashboardController::class, 'index'])->name('dashboard.academic');
    Route::get('/dashboard/professor/{teacher}',[TeacherDashboardController::class, 'index'])->name('dashboard.teacher');

    Route::get('/class-offerings/{offering}/disciplines/{discipline}/dashboard',[DisciplineDashboardController::class, 'index'])->name('class-offerings.disciplines.dashboard');

    Route::get('/dashboard/unit/{unit}',[UnitDashboardController::class, 'index'])->name('dashboard.unit');

    Route::get('/settings/alerts',[IntelligentAlertSettingController::class, 'edit'])->name('settings.alerts');
    Route::post('/settings/alerts',[IntelligentAlertSettingController::class, 'update'])->name('settings.alerts.update');

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('send', [PaymentController::class, 'create'])->name('create');
        Route::post('send', [PaymentController::class, 'store'])->name('store');

        Route::get('pending', [PaymentExecutionController::class, 'index'])->name('pending');
        Route::post('{payment}/pay', [PaymentExecutionController::class, 'pay'])->name('pay');

        Route::get('/dashboard', [PaymentDashboardController::class, 'index'])->name('dashboard');
    });

    Route::prefix('projects')->group(function () {
        Route::get('create/step1', [ProjectWizardController::class, 'createStep1'])->name('projects.create.step1');
        Route::post('store/step1', [ProjectWizardController::class, 'storeStep1'])->name('projects.store.step1');

        Route::get('create/step2/{project}', [ProjectWizardController::class, 'createStep2'])->name('projects.create.step2');
        Route::post('store/step2/{project}', [ProjectWizardController::class, 'storeStep2'])->name('projects.store.step2');

        Route::get('create/step3/{project}', [ProjectWizardController::class, 'createStep3'])->name('projects.create.step3');
        Route::post('store/step3/{project}', [ProjectWizardController::class, 'storeStep3'])->name('projects.store.step3');

        Route::get('create/step4/{project}', [ProjectWizardController::class, 'createStep4'])->name('projects.create.step4');
        Route::post('store/step4/{project}', [ProjectWizardController::class, 'storeStep4'])->name('projects.store.step4');

        Route::get('create/step5/{project}', [ProjectWizardController::class, 'createStep5'])->name('projects.create.step5');
        Route::post('store/step5/{project}', [ProjectWizardController::class, 'storeStep5'])->name('projects.store.step5');

        Route::get('review/{project}', [ProjectWizardController::class, 'review'])->name('projects.review');

        Route::post('finalize/{project}', [ProjectWizardController::class, 'finalize'])->name('projects.finalize');
    });

    Route::prefix('document-templates')->name('document-templates.')->group(function () {
        Route::get('/', [DocumentTemplateController::class, 'index'])->name('index');
        Route::get('{template}/edit', [DocumentTemplateController::class, 'edit'])->name('edit');
        Route::put('{template}', [DocumentTemplateController::class, 'update'])->name('update');
        Route::post('preview',[DocumentTemplateController::class, 'preview'])->name('preview');
    });

    Route::prefix('financial-reports')->name('financial-reports.')->group(function () {
        Route::get('/', [FinancialReportController::class, 'index'])->name('index');
        Route::get('/pdf', [FinancialReportController::class, 'pdf'])->name('pdf');
        Route::get('/excel', [FinancialReportController::class, 'excel'])->name('excel');

        Route::get('/scholarship-holder', [FinancialReportController::class, 'scholarshipHolder'])->name('scholarship-holder');
        Route::get('/scholarship-holder/pdf', [FinancialReportController::class, 'scholarshipHolderPdf'])->name('scholarship-holder.pdf');
        Route::get('/scholarship-holder/excel', [FinancialReportController::class, 'scholarshipHolderExcel'])->name('scholarship-holder.excel');

        // Já temos o relatório por bolsista
        // Agora adicionamos o por unidade / projeto
        Route::get('/unit-project', [FinancialReportController::class, 'unitProject'])->name('unit-project');
        Route::get('/unit-project/pdf', [FinancialReportController::class, 'unitProjectPdf'])->name('unit-project.pdf');
        Route::get('/unit-project/excel', [FinancialReportController::class, 'unitProjectExcel'])->name('unit-project.excel');

        Route::get('/institutional', [FinancialReportController::class, 'institutional'])->name('institutional');
        Route::get('/institutional/pdf', [FinancialReportController::class, 'institutionalPdf'])->name('institutional.pdf');
        Route::get('/institutional/excel', [FinancialReportController::class, 'institutionalExcel'])->name('institutional.excel');
    });

});