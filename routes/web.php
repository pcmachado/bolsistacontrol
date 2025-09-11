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
//require __DIR__.'/admin.php';

// Página de boas-vindas
Route::get('/', function () {
    return view('welcome');
});

// Área do Usuário (Bolsista)
// Rotas protegidas por autenticação e que só podem ser acessadas por bolsistas
Route::middleware(['auth', 'verified', 'role_or_permission:bolsista'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Módulo de Frequência para o Bolsista
    Route::prefix('frequencia')->name('frequencia.')->group(function () {
        Route::get('/registrar', [AttendanceController::class, 'create'])->name('create');
        Route::post('/registrar', [AttendanceController::class, 'store'])->name('store');
        Route::get('/historico', [AttendanceController::class, 'index'])->name('historico');
    });

    // Módulo de Notificações para o Bolsista
    Route::prefix('notificacoes')->name('notificacoes.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/marcar-lida', [NotificationController::class, 'markAsRead'])->name('marcarLida');
    });

    // Rotas de Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Área Administrativa (Coordenadores e Coordenadores Adjuntos)
// Rotas protegidas por autenticação e permissões do Spatie
Route::middleware(['auth', 'verified', 'role_or_permission:coordenador_geral'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    // Frequência
    Route::prefix('frequencia')
        ->name('frequencia.')
        ->group(function () {
            Route::get('/homologar', [AttendanceController::class, 'homologarIndex'])->name('homologar.index');
            Route::post('/{attendanceRecord}/homologar', [AttendanceController::class, 'homologar'])->name('homologar');
        });

    // Relatórios
    Route::prefix('reports')
        ->name('reports.')
        ->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::post('/gerar', [ReportController::class, 'gerarRelatorio'])->name('gerar');
            Route::get('/download/{filename}', [ReportController::class, 'download'])->name('download');
        });

    // Usuários
    Route::get('/users/data', [UserController::class, 'getData'])
        ->name('users.data');

    Route::resource('users', UserController::class)
        ->except(['show']);

    // Bolsistas
    Route::resource('scholarship_holders', ScholarshipHolderController::class)
        ->except(['show']);

    // Unidades
    Route::resource('units', UnitController::class)
        ->except(['show']);

    // Cargos
    Route::resource('positions', PositionController::class)
        ->except(['show']);
});
