<?php

use Illuminate\Support\Facades\Route;
use App\DataTables\UsersDataTable;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\ScholarshipHolderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HomologationController;       

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Módulo de Frequência para o Bolsista
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/registrar', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/historico', [AttendanceController::class, 'history'])->name('attendance.history');  
});

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified', 'role_or_permission:admin|coordenador_geral|coordenador_adjunto'])->prefix('admin')->name('admin.')->group(function () {
    Route::group(['middleware' => ['auth']], function() {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/users', function (UsersDataTable $dataTable) {
            return $dataTable->render('users.index');
        })->name('users.index');

        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
        Route::resource('units', UnitController::class);
        Route::resource('positions', PositionController::class);
        Route::resource('scholarship_holders', ScholarshipHolderController::class);
        Route::resource('projects', ProjectController::class);
        Route::resource('notifications', NotificationController::class);
        Route::resource('attendance_records', AttendanceRecordController::class);
        Route::resource('instituitions', InstitutionController::class);
        });
});