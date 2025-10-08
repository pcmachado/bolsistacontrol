<?php

use Illuminate\Support\Facades\Route;
use App\DataTables\UsersDataTable;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
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
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');
    // Módulo de Frequência para o Bolsista
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/registry', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/{id}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
    Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('attendance.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

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
        });
});