<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScholarshipHolderController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

    // Módulo Administrativo (requer permissão de administrador)
    Route::middleware(['can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

    // Rotas de recurso para Cargos (CRUD completo)
    Route::resource('cargos', PositionController::class);

// Rotas para o Módulo de Bolsistas
    Route::prefix('scholarship-holders')->name('bolsistas.')->group(function () {
        Route::get('/', [ScholarshipHolderController::class, 'index'])->name('index');
        Route::get('/criar', [ScholarshipHolderController::class, 'create'])->name('create');
        Route::post('/', [ScholarshipHolderController::class, 'store'])->name('store');
        // ... outras rotas de CRUD
    });

    // Rotas para o Módulo de Unidades
    Route::prefix('units')->name('unidades.')->group(function () {
        Route::get('/', [UnitController::class, 'index'])->name('index');
        Route::get('/criar', [UnitController::class, 'create'])->name('create');
        Route::post('/', [UnitController::class, 'store'])->name('store');
        // ... outras rotas de CRUD
    });

    // Rotas para o Módulo de Frequência
    Route::prefix('frequencia')->name('frequencia.')->group(function () {
        Route::get('/registrar', [AttendanceController::class, 'create'])->name('create');
        Route::post('/registrar', [AttendanceController::class, 'store'])->name('store');
        // ... rotas para visualização de histórico
    });

    // Rotas para o Módulo de Relatórios
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/gerar', [ReportController::class, 'gerarRelatorio'])->name('gerar');
        Route::get('/download/{filename}', [ReportController::class, 'download'])->name('download');
    });

    // Rotas para o Módulo de Notificações
    Route::prefix('notificacoes')->name('notificacoes.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notificacao}/marcar-lida', [NotificationController::class, 'marcarLida'])->name('marcarLida');
    });

require __DIR__.'/auth.php';
