<?php

use App\Http\Controllers\Admin\AcademicDashboardController;
use App\Http\Controllers\Admin\AdminScholarshipHolderController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\ClassOfferingController;
use App\Http\Controllers\Admin\ClassOfferingDashboardController;
use App\Http\Controllers\Admin\ClassOfferingDisciplineController;
use App\Http\Controllers\Admin\ClassOfferingScholarshipHolderController;
use App\Http\Controllers\Admin\ClassOfferingStudentController;
use App\Http\Controllers\Admin\ClassOfferingSyllabusController;
use App\Http\Controllers\Admin\ClassSessionController;
use App\Http\Controllers\Admin\ClassSessionReportController;
use App\Http\Controllers\Admin\ContextController;
use App\Http\Controllers\Admin\CourseClassOfferingController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseDisciplineController;
use App\Http\Controllers\Admin\CourseScholarshipHolderController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DisciplineController;
use App\Http\Controllers\Admin\DisciplineDashboardController;
use App\Http\Controllers\Admin\DocumentTemplateController;
use App\Http\Controllers\Admin\FinancialClosureController;
use App\Http\Controllers\Admin\FinancialReportController;
use App\Http\Controllers\Admin\FundingSourceController;
use App\Http\Controllers\Admin\HomologationController;
use App\Http\Controllers\Admin\InstitutionController;
use App\Http\Controllers\Admin\IntelligentAlertSettingController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentDashboardController;
use App\Http\Controllers\Admin\PaymentExecutionController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectEditController;
use App\Http\Controllers\Admin\ProjectWizardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StudentPaymentController;
use App\Http\Controllers\Admin\SuperAdminDashboardController;
use App\Http\Controllers\Admin\SupervisorAssignmentController;
use App\Http\Controllers\Admin\TeacherClassController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TeacherDashboardController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UnitDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\AttendanceSubmissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardResolverController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\FinalActivityReportController;
use App\Http\Controllers\MyAttendanceRecordController;
use App\Http\Controllers\MyAttendanceSubmissionController;
use App\Http\Controllers\MyPaymentController;
use App\Http\Controllers\MonthlyConsolidatedReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationSettingController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptVerificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScholarshipHolderController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Auth\IFRSAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SystemReleaseController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/manual/{doc?}', function (?string $doc = null) {
    $docs = [
        'guia-executivo' => 'GUIA-EXECUTIVO.md',
        'readme' => 'README.md',
        'bolsista' => 'perfis/bolsista.md',
        'coordenacao' => 'perfis/coordenacao.md',
        'coordenador-adjunto' => 'perfis/coordenador-adjunto.md',
        'coordenador-adjunto-geral' => 'perfis/coordenador-adjunto-geral.md',
        'coordenador-geral' => 'perfis/coordenador-geral.md',
        'professor' => 'perfis/professor.md',
        'orientador' => 'perfis/orientador.md',
        'admin' => 'perfis/admin.md',
        'professor-supervisor' => 'perfis/professor-supervisor.md',
        'turmas-alunos' => 'perfis/turmas-alunos.md',
    ];

    $selectedDoc = $doc ?: 'guia-executivo';
    abort_unless(isset($docs[$selectedDoc]), 404);

    return view('manual.index', [
        'manualDocs' => $docs,
        'selectedDoc' => $selectedDoc,
    ]);
})->middleware(['auth', 'verified'])->name('manual.index');

// Receipt Verification
Route::get('/verificar-recibo', [ReceiptVerificationController::class, 'form'])->name('payments.verify.form');
Route::post('/verificar-recibo', [ReceiptVerificationController::class, 'verify'])->name('payments.verify');

Route::prefix('login/ifrs')->group(function () {

    Route::get('/redirect', [IFRSAuthController::class,'redirect'])->name('login.ifrs.redirect');
    Route::get('/callback', [IFRSAuthController::class,'callback'])->name('login.ifrs.callback');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('/user/mark-version-seen', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'version' => 'required|string|max:50',
        ]);

        if (auth()->check()) {
            auth()->user()->update([
                'last_seen_version' => \App\Models\SystemRelease::normalizeVersion($validated['version']),
            ]);
        }
        return response()->json(['success' => true]);
    })->name('user.mark_version_seen');

    Route::get('/meu-dashboard', [DashboardController::class, 'index'])->name('holder.dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    Route::post('/impersonate-stop', [AdminScholarshipHolderController::class, 'stop'])->name('admin.impersonate.stop');

    // Módulo de Frequência para o Bolsista
    Route::prefix('attendance')->middleware('auth')->group(function () {
        /*
        |--------------------------------------------------------------------------
        | REGISTROS DIÁRIOS (BOLSISTA)
        |--------------------------------------------------------------------------
        */
        Route::get('/', [AttendanceRecordController::class, 'index'])->name('attendance.index');
        Route::get('/my', [MyAttendanceRecordController::class, 'index'])->name('attendance.my');
        Route::get('/create', [AttendanceRecordController::class, 'create'])->name('attendance.create');
        Route::post('/', [AttendanceRecordController::class, 'store'])->name('attendance.store');

        /*
        |--------------------------------------------------------------------------
        | SUBMISSÕES MENSAIS
        |--------------------------------------------------------------------------
        */
        Route::prefix('submissions')->group(function () {
            Route::get('/', [AttendanceSubmissionController::class, 'index'])->name('attendance.submissions.index');
            Route::post('/', [AttendanceSubmissionController::class, 'store'])->name('attendance.submissions.store');

            Route::get('my', [MyAttendanceSubmissionController::class, 'index'])->name('my-attendance.submissions.my');
            Route::post('my', [MyAttendanceSubmissionController::class, 'store'])->name('my-attendance.submissions.store');
            Route::post('my/submit-month', [MyAttendanceSubmissionController::class, 'submitMonth'])->name('my-attendance.submissions.submit-month');
            Route::post('my/submit-all-month', [MyAttendanceSubmissionController::class, 'submitAllMonth'])->name('my-attendance.submissions.submit-all-month');

            Route::get('/{submission}', [AttendanceSubmissionController::class, 'show'])->name('attendance.submissions.show');
            Route::post('/{submission}/submit', [AttendanceSubmissionController::class, 'submit'])->name('attendance.submissions.submit');
            Route::post('/{submission}/approve', [AttendanceSubmissionController::class, 'approve'])->name('attendance.submissions.approve');
            Route::post('/{submission}/reject', [AttendanceSubmissionController::class, 'reject'])->name('attendance.submissions.reject');
            Route::delete('/{submission}/records/{record}', [AttendanceSubmissionController::class, 'removeRecord'])->name('attendance.submissions.records.remove');

            Route::get('/my/{submission}', [MyAttendanceSubmissionController::class, 'show'])->name('my-attendance.submissions.show');
            Route::post('/my/{submission}/submit', [MyAttendanceSubmissionController::class, 'submit'])->name('my-attendance.submissions.submit');
            Route::post('/my/{submission}/approve', [MyAttendanceSubmissionController::class, 'approve'])->name('my-attendance.submissions.approve');
            Route::post('/my/{submission}/reject', [MyAttendanceSubmissionController::class, 'reject'])->name('my-attendance.submissions.reject');
            Route::delete('/my/{submission}/records/{record}', [MyAttendanceSubmissionController::class, 'removeRecord'])->name('my-attendance.submissions.records.remove');

            Route::get('/cards/approved/{month}', fn ($month) => null)->name('attendance.submissions.cards.approved.month');
            Route::get('/cards/submitted/{month}', fn ($month) => null)->name('attendance.submissions.cards.submitted.month');
            Route::get('/cards/rejected/{month}', fn ($month) => null)->name('attendance.submissions.cards.rejected.month');
            Route::get('/cards/late/{month}', fn ($month) => null)->name('attendance.submissions.cards.late.month');

        });

        /*
        |--------------------------------------------------------------------------
        | RELATÓRIOS DO BOLSISTA
        |--------------------------------------------------------------------------
        */
        Route::prefix('reports')->group(function () {

            Route::get('/', [AttendanceReportController::class, 'index'])->name('attendance.reports.index');
            Route::get('/monthly/{submission}', [AttendanceReportController::class, 'monthly'])->name('attendance.reports.monthly');
            Route::get('/monthly/{submission}/blank', [AttendanceReportController::class, 'monthlyBlank'])->name('attendance.reports.monthly.blank');
            Route::get('/monthly/{submission}/pdf', [AttendanceReportController::class, 'monthlyPdf'])->name('attendance.reports.monthly.pdf');
            Route::get('/monthly-consolidated', [MonthlyConsolidatedReportController::class, 'index'])->name('attendance.reports.monthly-consolidated');

            Route::prefix('final')->group(function () {
                Route::get('/', [FinalActivityReportController::class, 'index'])->name('attendance.reports.final.index');
                Route::post('/blank', [FinalActivityReportController::class, 'blank'])->name('attendance.reports.final.blank');
                Route::get('/create', [FinalActivityReportController::class, 'create'])->name('attendance.reports.final.create');
                Route::post('/', [FinalActivityReportController::class, 'store'])->name('attendance.reports.final.store');

                Route::get('/{report}/edit', [FinalActivityReportController::class, 'edit'])->name('attendance.reports.final.edit');
                Route::put('/{report}', [FinalActivityReportController::class, 'update'])->name('attendance.reports.final.update');
                Route::get('/{report}', [FinalActivityReportController::class, 'show'])->name('attendance.reports.final.show');
                Route::get('/{report}/pdf', [FinalActivityReportController::class, 'pdf'])->name('attendance.reports.final.pdf');
                Route::post('/{report}/submit', [FinalActivityReportController::class, 'submit'])->name('attendance.reports.final.submit');
                Route::post('/{report}/approve', [FinalActivityReportController::class, 'approve'])->name('attendance.reports.final.approve');
            });
        });

        /*
        |--------------------------------------------------------------------------
        | REGISTRO INDIVIDUAL (SEMPRE POR ÚLTIMO)
        |--------------------------------------------------------------------------
        */
        Route::get('/{record}', [AttendanceRecordController::class, 'show'])->whereNumber('record')->name('attendance.show');
        Route::get('/{record}/edit', [AttendanceRecordController::class, 'edit'])->whereNumber('record')->name('attendance.edit');
        Route::put('/{record}', [AttendanceRecordController::class, 'update'])->whereNumber('record')->name('attendance.update');
        Route::delete('/{record}', [AttendanceRecordController::class, 'destroy'])->whereNumber('record')->name('attendance.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all', [NotificationController::class, 'markAll'])->name('notifications.markAll');

    Route::get('/notifications/read/{id}', [NotificationController::class, 'read'])->name('notifications.read');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');

    Route::prefix('payments')->as('payments.')->group(function () {
        Route::get('/my', [MyPaymentController::class, 'myPayments'])->name('my');

        Route::get('/my/report', [MyPaymentController::class, 'reportMy'])->name('my.report');

        Route::post('/{payment}/confirm', [MyPaymentController::class, 'confirm'])->name('confirm');
        Route::get('/{payment}/receipt', [MyPaymentController::class, 'receipt'])->name('receipt');
    });

    // Rotas para Professores
    Route::prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/classes', [TeacherClassController::class, 'index'])->name('classes');

        Route::get('/classes/{offering}', [TeacherClassController::class, 'show'])->name('classes.show');
        Route::post('/classes/{offering}/monthly-save', [TeacherClassController::class, 'storeMonthly'])->name('classes.monthly.save');
        Route::post('/classes/{offering}/{month}/close', [TeacherClassController::class, 'closeMonth'])->name('classes.monthly.close');
    });

});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardResolverController::class, 'index'])->name('dashboard');

    Route::prefix('superadmin')->middleware('role:superadmin')->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('superadmin.dashboard');
    });
});

Route::get('/scholarship_holders/search', [ScholarshipHolderController::class, 'search'])
    ->middleware(['auth', 'verified'])
    ->name('scholarshipholders.search');
Route::get('/admin/scholarship-holders/search', [ScholarshipHolderController::class, 'search'])
    ->middleware(['auth', 'verified'])
    ->name('admin.scholarship-holders.search');

Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');

Route::post('/admin/context/switch', [ContextController::class, 'switch'])->name('admin.context.switch')->middleware(['auth', 'role:admin|superadmin']);

Route::get('/api/institutions/{id}/units', function ($id) {
    return \App\Models\Unit::where('institution_id', $id)->get(['id', 'name']);
});

Route::get('/api/users/search', [UserController::class, 'search'])
    ->middleware(['auth', 'verified']);

// Rotas Administrativas
Route::middleware(['auth', 'verified', 'role_or_permission:superadmin|admin|coordenador_geral|coordenador_adjunto_geral|coordenador_adjunto'])->prefix('admin')->name('admin.')->group(function () {

    Route::prefix('impersonate/holders')->name('impersonate.holders.')->group(function () {
        Route::get('/', [AdminScholarshipHolderController::class, 'index'])->name('index');
        Route::get('{holder}', [AdminScholarshipHolderController::class, 'show'])->name('show');
        Route::get('{holder}/edit', [AdminScholarshipHolderController::class, 'edit'])->name('edit');
        Route::put('{holder}', [AdminScholarshipHolderController::class, 'update'])->name('update');
    });

    // Impersonate
    Route::post('impersonate/{user}', [AdminScholarshipHolderController::class, 'start'])->name('impersonate');

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');

    // 🔹 Gerenciamento de Bolsistas por Curso (apenas coordenadores)
    Route::prefix('courses/{course}/holders')->name('courses.holders.')->group(function () {

        Route::get('/', [CourseScholarshipHolderController::class, 'index'])->name('index');

        Route::post('/', [CourseScholarshipHolderController::class, 'store'])->name('store');

        Route::put('/{holder}', [CourseScholarshipHolderController::class, 'update'])->name('update');

        Route::delete('/{holder}', [CourseScholarshipHolderController::class, 'destroy'])->name('destroy');

    });

    // 🔹 Relatório de Homologações (apenas coordenador geral e adjunto)
    Route::prefix('homologations')->name('homologations.')->group(function () {
        Route::get('/report', [HomologationController::class, 'report'])->name('report');
        Route::post('/bulk', [HomologationController::class, 'bulk'])->name('bulk');
        Route::get('/pending', [HomologationController::class, 'pending'])->name('pending');
        Route::get('/', [HomologationController::class, 'index'])->name('index');
        Route::get('/late', [HomologationController::class, 'late'])->name('late');

        Route::get('/{submission}', [HomologationController::class, 'show'])->name('show');

        Route::post('{submission}/approve', [HomologationController::class, 'approve'])->name('approve');
        Route::post('{submission}/reject', [HomologationController::class, 'reject'])->name('reject');
    });

    // 🔹 Relatório de Fechamento Financeiro (apenas coordenador geral e adjunto)
    Route::prefix('academic-reports/class-sessions')->name('academic-reports.class-sessions.')->group(function () {
        Route::get('/global', [ClassSessionReportController::class, 'global'])->name('global');
        Route::get('/global/pdf', [ClassSessionReportController::class, 'exportPdf'])->name('pdf');
        Route::get('/global/excel', [ClassSessionReportController::class, 'exportExcel'])->name('excel');
        Route::get('/{offering}', [ClassSessionReportController::class, 'index'])->name('index');
    });

    // 🔹 Relatório Consolidado (apenas coordenador geral)
    Route::get('/reports/monthly', [ReportController::class, 'monthlyReport'])->name('reports.report');
    Route::get('/reports/pdf', [ReportController::class, 'reportPdf'])->name('reports.export_pdf');
    Route::get('/reports/excel', [ReportController::class, 'reportExcel'])->name('reports.export_excel');

    // 🔹 Relatório Detalhado por Unidade (coordenador geral)
    Route::get('/reports/unit/{unit?}/{project?}', [ReportController::class, 'unitDetail'])->name('reports.unit_detail');

    Route::resource('assignments', AssignmentController::class);
    Route::resource('roles', RoleController::class);
    Route::post('users/{user}/resend-verification', [UserController::class, 'resendVerification'])->name('users.resend-verification');
    Route::resource('users', UserController::class);
    Route::resource('units', UnitController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('scholarship_holders', ScholarshipHolderController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('notifications', NotificationController::class);
    Route::resource('email-templates', EmailTemplateController::class);
    Route::resource('notification-settings', NotificationSettingController::class);
    Route::resource('attendance_records', AttendanceRecordController::class);
    Route::resource('institutions', InstitutionController::class);
    Route::resource('reports', ReportController::class)->except(['show']);
    // Route::resource('homologations', HomologationController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('courses', CourseController::class);
    Route::resource('funding-sources', FundingSourceController::class);
    Route::resource('projects', ProjectController::class);
    // Route::resource('assignments', AssignmentController::class);
    // CRUD de Versões do Sistema
    Route::post('system_releases/import-from-git', [SystemReleaseController::class, 'importFromGit'])
        ->name('system_releases.import-git');
    Route::resource('system_releases', SystemReleaseController::class)->except(['show']);

    Route::resource('disciplines', DisciplineController::class);

    Route::resource('supervisors', SupervisorAssignmentController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('students', StudentController::class);

    // 1. Recurso Principal (CRUD padrão)
    Route::resource('class-offerings', ClassOfferingController::class);

    // 2. Grupo de Rotas Aninhadas a uma Oferta específica
    Route::prefix('class-offerings/{offering}')->as('class-offerings.')->group(function () {

        // Dashboard e Syllabus da Oferta
        Route::get('dashboard', [ClassOfferingDashboardController::class, 'index'])->name('dashboard');
        Route::get('syllabus', [ClassOfferingSyllabusController::class, 'index'])->name('syllabus');

        // Disciplinas da Oferta
        Route::controller(ClassOfferingDisciplineController::class)->as('disciplines.')->group(function () {
            Route::get('disciplines', [ClassOfferingDisciplineController::class, 'index'])->name('index');
            Route::post('disciplines', [ClassOfferingDisciplineController::class, 'store'])->name('store');
            // Dashboard específico da disciplina dentro da oferta
            Route::get('disciplines/{discipline}/dashboard', [DisciplineDashboardController::class, 'index'])->name('dashboard');
        });

        // Bolsistas (Scholarship Holders)
        Route::controller(ClassOfferingScholarshipHolderController::class)->as('scholarship_holders.')->group(function () {
            Route::get('scholarship_holders', [ClassOfferingScholarshipHolderController::class, 'index'])->name('index');
            Route::post('scholarship_holders', [ClassOfferingScholarshipHolderController::class, 'store'])->name('store');
            Route::delete('scholarship_holders/{scholarshipHolder}', [ClassOfferingScholarshipHolderController::class, 'destroy'])->name('destroy');
        });

        // Sessões e Relatórios de Sessão
        Route::get('sessions', [ClassSessionController::class, 'index'])->name('sessions.index');
        Route::post('sessions', [ClassSessionController::class, 'store'])->name('sessions.store');
        Route::delete('sessions/{session}', [ClassSessionController::class, 'destroy'])->name('sessions.destroy');

        Route::controller(ClassSessionReportController::class)->as('sessions.report.')->group(function () {
            Route::get('sessions/report', [ClassSessionReportController::class, 'index'])->name('index');
            Route::get('sessions/report/pdf', [ClassSessionReportController::class, 'exportPdf'])->name('pdf');
            Route::get('sessions/report/excel', [ClassSessionReportController::class, 'exportExcel'])->name('excel');
        });
    });

    // 3. Rotas de Pivot/Individuais (fora do grupo de prefixo de oferta, se o {pivot} for único)
    Route::controller(ClassOfferingDisciplineController::class)->as('class-offerings.disciplines.')->group(function () {
        Route::put('class-offerings/discipline/{pivot}', [ClassOfferingDisciplineController::class, 'update'])->name('update');
        Route::delete('class-offerings/discipline/{pivot}', [ClassOfferingDisciplineController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('class-offerings')->group(function () {
        Route::get('{class}/students/list', [ClassOfferingStudentController::class, 'list'])->name('class.students.list');
        Route::get('{class}/students', [ClassOfferingStudentController::class, 'index'])->name('class.students.index');
        Route::post('{class}/students', [ClassOfferingStudentController::class, 'save'])->name('class.students.save');
        Route::post('{class}/students/submit', [ClassOfferingStudentController::class, 'submit'])->name('class.students.submit');
    });

    Route::prefix('student-payments')->name('student-payments.')->group(function () {

        Route::get('/', [StudentPaymentController::class, 'index'])->name('index');
        Route::post('batch/pay', [StudentPaymentController::class, 'payBatch'])->name('payBatch');
        Route::get('dashboard', [StudentPaymentController::class, 'dashboard'])->name('dashboard');

        Route::get('report/pdf', [StudentPaymentController::class, 'reportPdf'])->name('report.pdf');
        Route::get('report/excel', [StudentPaymentController::class, 'reportExcel'])->name('report.excel');

        Route::post('{payment}/pay', [StudentPaymentController::class, 'pay'])->name('pay');

    });

    // 4. Dashboards Globais e Relatórios Gerais
    Route::prefix('dashboard')->as('dashboard.')->group(function () {
        Route::get('academic', [AcademicDashboardController::class, 'index'])->name('academic');
        Route::get('risk', [AcademicDashboardController::class, 'risk'])->name('risk');
        Route::get('unit/{unit}', [UnitDashboardController::class, 'index'])->name('unit');
    });

    Route::get('/settings/alerts', [IntelligentAlertSettingController::class, 'edit'])->name('settings.alerts');
    Route::post('/settings/alerts', [IntelligentAlertSettingController::class, 'update'])->name('settings.alerts.update');

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/dashboard', [PaymentDashboardController::class, 'index'])->name('dashboard');

        Route::get('send', [PaymentController::class, 'create'])->name('create');
        Route::post('send', [PaymentController::class, 'store'])->name('store');

        Route::get('/', [PaymentController::class, 'index'])->name('index');

        Route::get('batch', [PaymentController::class, 'batchForm'])->name('batch.form');
        Route::post('batch/preview', [PaymentController::class, 'batchPreview'])->name('batch.preview');
        Route::post('batch/store', [PaymentController::class, 'batchStore'])->name('batch.store');

        Route::get('/report/monthly', [PaymentController::class, 'reportMonthly'])->name('reports.monthly');

        Route::post('{payment}/pay', [PaymentExecutionController::class, 'pay'])->name('pay');
        Route::post('{payment}/confirm', [PaymentExecutionController::class, 'confirm'])->name('confirm');
        Route::get('{payment}/receipt', [PaymentReceiptController::class, 'download'])->name('receipt');

        Route::get('/{payment}', [PaymentController::class, 'show'])->whereNumber('payment')->name('show');
    });

    Route::prefix('projects/wizard')->name('projects.')->group(function () {
        // STEP 1 — Projeto
        Route::get('create', [ProjectWizardController::class, 'createStep1'])->name('create.step1');
        Route::post('create', [ProjectWizardController::class, 'storeStep1'])->name('store.step1');
        Route::get('{project}/step-1', [ProjectWizardController::class, 'editStep1'])->name('edit.step1');
        Route::post('{project}/step-1', [ProjectWizardController::class, 'updateStep1'])->name('update.step1');

        // STEP 2 — Cargos
        Route::get('{project}/step-2', [ProjectWizardController::class, 'createStep2'])->name('create.step2');
        Route::post('{project}/step-2', [ProjectWizardController::class, 'storeStep2'])->name('store.step2');

        // STEP 3 — Cursos
        Route::get('{project}/step-3', [ProjectWizardController::class, 'createStep3'])->name('create.step3');
        Route::post('{project}/step-3', [ProjectWizardController::class, 'storeStep3'])->name('store.step3');

        // STEP 4 — Bolsistas
        Route::get('{project}/step-4', [ProjectWizardController::class, 'createStep4'])->name('create.step4');
        Route::post('{project}/step-4', [ProjectWizardController::class, 'storeStep4'])->name('store.step4');

        // STEP 5 — Fomento
        Route::get('{project}/step-5', [ProjectWizardController::class, 'createStep5'])->name('create.step5');
        Route::post('{project}/step-5', [ProjectWizardController::class, 'storeStep5'])->name('store.step5');

        // REVIEW
        Route::get('{project}/review', [ProjectWizardController::class, 'review'])->name('review');

        // FINALIZE
        Route::post('{project}/finalize', [ProjectWizardController::class, 'finalize'])->name('finalize');
    });

    Route::prefix('projects/{project}/edit')->name('projects.edit.')->middleware(['auth', 'verified'])->group(function () {

        Route::get('/', [ProjectEditController::class, 'index'])->name('index');

        Route::get('/general', [ProjectEditController::class, 'general'])->name('general');
        Route::get('/positions', [ProjectEditController::class, 'positions'])->name('positions');
        Route::put('/positions', [ProjectEditController::class, 'updatePositions'])->name('positions.update');

        Route::get('/scholars', [ProjectEditController::class, 'scholars'])->name('scholars');
        Route::post('/scholars', [ProjectEditController::class, 'storeScholar'])->name('scholars.store');
        Route::put('/scholars', [ProjectEditController::class, 'updateScholar'])->name('scholars.update');

        Route::get('/scholars/{holder}/edit', [ProjectEditController::class, 'editScholar'])->name('scholars.edit');
        Route::delete('/scholars/{holder}', [ProjectEditController::class, 'destroyScholar'])->name('scholars.destroy');

        Route::get('/courses', [ProjectEditController::class, 'courses'])->name('courses');
        Route::post('/courses', [ProjectEditController::class, 'updateCourses'])->name('courses.update');

        Route::get('/funding', [ProjectEditController::class, 'funding'])->name('funding');
        Route::post('/funding', [ProjectEditController::class, 'updateFunding'])->name('funding.update');
    });

    Route::prefix('document-templates')->name('document-templates.')->group(function () {
        Route::get('/', [DocumentTemplateController::class, 'index'])->name('index');
        Route::get('/create', [DocumentTemplateController::class, 'create'])->name('create');
        Route::post('/', [DocumentTemplateController::class, 'store'])->name('store');
        Route::post('/preview', [DocumentTemplateController::class, 'preview'])->name('preview');

        Route::get('{template}/edit', [DocumentTemplateController::class, 'edit'])->name('edit');
        Route::put('{template}', [DocumentTemplateController::class, 'update'])->name('update');
        Route::get('/{template}', [DocumentTemplateController::class, 'show'])->name('show');
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

    Route::prefix('courses/{course}')->name('courses.')->group(function () {
        Route::get('disciplines', [CourseDisciplineController::class, 'index'])->name('disciplines.index');
        Route::post('disciplines', [CourseDisciplineController::class, 'store'])->name('disciplines.store');

        Route::get('class-offerings', [CourseClassOfferingController::class, 'index'])->name('class-offerings.index');
        Route::get('class-offerings/create', [CourseClassOfferingController::class, 'create'])->name('class-offerings.create');
    });

    Route::get('/financial-closures/', [FinancialClosureController::class, 'index'])->name('financial-closures.index');
    Route::post('/financial-closures', [FinancialClosureController::class, 'store'])->name('financial-closures.store');
    Route::get('/financial-closures/preview', [FinancialClosureController::class, 'preview'])->name('financial-closures.preview');

});
