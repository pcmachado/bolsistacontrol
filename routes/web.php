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
use App\Http\Controllers\Admin\InstitutionController;
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
    Route::get('/attendance', [AttendanceRecordController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/registry', [AttendanceRecordController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceRecordController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/history', [AttendanceRecordController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/{id}/edit', [AttendanceRecordController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{id}', [AttendanceRecordController::class, 'update'])->name('attendance.update');
    Route::delete('/attendance/{id}', [AttendanceRecordController::class, 'destroy'])->name('attendance.destroy');
    Route::get('/attendance/{id}', [AttendanceRecordController::class, 'show'])->name('attendance.show');
    Route::get('/attendance/pending', [AttendanceRecordController::class, 'pending'])->name('attendance.pending');

    Route::get('/attendance/card/approved', [AttendanceRecordController::class, 'approved'])->name('attendance.card.approved');
    Route::get('/attendance/card/pending', [AttendanceRecordController::class, 'pending'])->name('attendance.card.pending');
    Route::get('/attendance/card/rejected', [AttendanceRecordController::class, 'rejected'])->name('attendance.card.rejected');
    Route::get('/attendance/card/late', [AttendanceRecordController::class, 'late'])->name('attendance.card.late');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/my-report', [ReportController::class, 'individualReport'])->name('reports.myReport');
    Route::get('/monthly', [ReportController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('/unit/{unit}', [ReportController::class, 'unitDetail'])->name('reports.unit');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');

});

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified', 'role_or_permission:Admin|coordenador_geral|coordenador_adjunto'])->prefix('admin')->name('admin.')->group(function () {
    Route::group(['middleware' => ['auth']], function() {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::post('/{record}/approve', [HomologationController::class, 'approve'])->name('homologations.approve');
        Route::post('/{record}/reject', [HomologationController::class, 'reject'])->name('homologations.reject');

        // 🔹 Relatório de Homologações (apenas coordenador geral e adjunto)
        Route::get('/homologations/report', [HomologationController::class, 'report'])->name('homologations.report');

        // 🔹 Relatório Consolidado (apenas coordenador geral)
        Route::get('/monthly', [ReportController::class, 'monthlyReport'])->middleware('role:coordenador_geral')->name('reports.monthly');

        // 🔹 Relatório Detalhado por Unidade (coordenador geral)
        Route::get('/unit/{unit}', [ReportController::class, 'unitDetail'])->middleware('role:coordenador_geral')->name('reports.unit');

        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
        Route::resource('units', UnitController::class);
        Route::resource('positions', PositionController::class);
        Route::resource('scholarship_holders', ScholarshipHolderController::class);
        Route::resource('projects', ProjectController::class);
        Route::resource('notifications', NotificationController::class);
        Route::resource('attendance_records', AttendanceRecordController::class);
        Route::resource('instituitions', InstitutionController::class);
        Route::resource('reports', ReportController::class);
        Route::resource('homologations', HomologationController::class);
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
            Route::post('finish/{project}', [ProjectWizardController::class, 'finish'])->name('projects.finish');
        });

    });
});