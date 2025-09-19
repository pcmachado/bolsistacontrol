<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\ScholarshipHolderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

// Rotas de Autenticação (Laravel Breeze)
require __DIR__.'/auth.php';

// Página de boas-vindas
Route::get('/', function () {

    return view('welcome');
});

// Área do Utilizador (Bolsista) - Rotas para utilizadores autenticados
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Módulo de Frequência para o Bolsista
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/registrar', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    // Módulo de Notificações para o Bolsista
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // Rotas de Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Área Administrativa - Rotas protegidas por autenticação e pelo role de 'admin'
Route::middleware(['auth', 'verified', 'role_or_permission:admin|coordenador_geral|coordenador_adjunto'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard Administrativo
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Frequência
    Route::prefix('attendance')
        ->name('attendance.')
        ->group(function () {
            Route::get('/homologar', [AttendanceController::class, 'homologarIndex'])->name('homologar.index');
            Route::post('/{attendanceRecord}/homologar', [AttendanceController::class, 'homologar'])->name('homologar');
        });
    Route::resource('attendance', AttendanceController::class)->except(['show']);


    // Módulo de Relatórios
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/gerar', [ReportController::class, 'gerarRelatorio'])->name('gerar');
        Route::get('/download/{filename}', [ReportController::class, 'download'])->name('download');
    });

    // Módulos de Gerenciamento (RESTful Resources)
    Route::resource('users', UserController::class)->except(['show']);
    
    Route::resource('scholarship_holders', ScholarshipHolderController::class)->except(['show']);
    Route::resource('units', UnitController::class)->except(['show'])->names('units'); // Usando 'units' para corresponder à sidebar
    Route::resource('positions', PositionController::class)->except(['show'])->names('positions'); // Usando 'positions' para corresponder à sidebar

});