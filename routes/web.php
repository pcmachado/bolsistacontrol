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
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::resource('/dashboard', DashboardController::class)->only(['index'])->names(['index' => 'dashboard']);

    // Módulo de Frequência para o Bolsista
    // --- Minhas Frequências (sempre só os próprios registros) ---
    Route::get('/attendance/my', [AttendanceRecordController::class, 'index'])->name('attendance.my');
    Route::get('/attendance/create', [AttendanceRecordController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceRecordController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/{attendanceRecord}/edit', [AttendanceRecordController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{attendanceRecord}', [AttendanceRecordController::class, 'update'])->name('attendance.update');
    Route::delete('/attendance/{attendanceRecord}', [AttendanceRecordController::class, 'destroy'])->name('attendance.destroy');

    Route::get('/attendance/history', [AttendanceRecordController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/pending', [AttendanceRecordController::class, 'pending'])->name('attendance.pending');
    Route::post('/attendance/{attendanceRecord}/submit', [AttendanceRecordController::class, 'submit'])->name('attendance.submit');

    Route::get('/attendance/card/approved', [AttendanceRecordController::class, 'approved'])->name('attendance.card.approved');
    Route::get('/attendance/card/submitted', [AttendanceRecordController::class, 'submitted'])->name('attendance.card.submitted');
    Route::get('/attendance/card/rejected', [AttendanceRecordController::class, 'rejected'])->name('attendance.card.rejected');
    Route::get('/attendance/card/late', [AttendanceRecordController::class, 'late'])->name('attendance.card.late');

    Route::get('/attendance/submissions', [AttendanceRecordController::class, 'submissions'])->name('attendance.submissions');
    Route::get('/attendance/approvals', [AttendanceRecordController::class, 'approvals'])->name('attendance.approvals');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/reports/my-report', [ReportController::class, 'individualReport'])->name('reports.myReport');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');

});

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/scholarship_holders/search', [ScholarshipHolderController::class, 'search'])->name('scholarshipholders.search');
Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');

Route::middleware(['auth', 'verified', 'role_or_permission:Admin|coordenador_geral|coordenador_adjunto'])->prefix('admin')->name('admin.')->group(function () {
    Route::group(['middleware' => ['auth']], function() {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');

        Route::post('/{record}/approve', [HomologationController::class, 'approve'])->name('homologations.approve');
        Route::post('/{record}/reject', [HomologationController::class, 'reject'])->name('homologations.reject');

        // 🔹 Relatório de Homologações (apenas coordenador geral e adjunto)
        Route::get('/homologations/report', [HomologationController::class, 'report'])->name('homologations.report');

        Route::post('/homologations/bulk', [HomologationController::class, 'bulk'])->name('homologations.bulk');
        Route::get('/homologations/pending', [HomologationController::class, 'pending'])->name('homologations.pending');
        Route::get('/homologations/{id}', [HomologationController::class, 'show'])->name('homologations.show');
        Route::get('/homologations', [HomologationController::class, 'index'])->name('homologations.index');
        Route::get('/homologations/late', [HomologationController::class, 'late'])->name('homologations.late');

        // 🔹 Relatório Consolidado (apenas coordenador geral)
        Route::get('/reports/monthly', [ReportController::class, 'monthlyReport'])->name('reports.report');
        Route::get('/reports/pdf', [ReportController::class, 'reportPdf'])->name('reports.export_pdf');
        Route::get('/reports/excel', [ReportController::class, 'reportExcel'])->name('reports.export_excel');

        // 🔹 Relatório Detalhado por Unidade (coordenador geral)
        Route::get('/reports/unit/{unit?}', [ReportController::class, 'unitDetail'])->name('reports.unit_detail');

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

    });
});