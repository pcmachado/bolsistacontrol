<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\PositionController;
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

// Área do Usuário (Bolsista)
// Rotas protegidas por autenticação e que só podem ser acessadas por bolsistas
Route::middleware(['auth', 'verified', 'role:bolsista'])->group(function () {
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
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    
    // Rota para o dashboard administrativo, acessível por ambos os tipos de coordenadores
    Route::get('/', [AdminDashboardController::class, 'index'])
        ->middleware('permission:acessar dashboard coordenador')
        ->name('dashboard');

    // Módulo de Homologação de Frequência (apenas para Coordenadores Adjuntos)
    Route::prefix('frequencia')->name('frequencia.')->middleware('permission:homologar frequencia')->group(function () {
        Route::get('/homologar', [AttendanceController::class, 'homologarIndex'])->name('homologar.index');
        Route::post('/{attendanceRecord}/homologar', [AttendanceController::class, 'homologar'])->name('homologar');
    });

    // Módulo de Relatórios (apenas para a Coordenacao Geral)
    Route::prefix('relatorios')->name('relatorios.')->middleware('permission:gerar relatorios')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/gerar', [ReportController::class, 'gerarRelatorio'])->name('gerar');
        Route::get('/download/{filename}', [ReportController::class, 'download'])->name('download');
    });

    // Módulo de Gerenciamento de Usuários (apenas para a Coordenacao Geral)
    Route::resource('users', UserController::class)->except(['show'])
        ->middleware('permission:gerenciar usuarios');

    // Módulo de Bolsistas (apenas para Coordenadores Adjuntos e Gerais)
    Route::resource('bolsistas', ScholarshipHolderController::class)
        ->except(['show'])
        ->middleware('permission:gerenciar bolsistas');
        
    // Módulo de Unidades (apenas para Coordenadores Adjuntos e Gerais)
    Route::resource('unidades', UnitController::class)
        ->except(['show'])
        ->middleware('permission:gerenciar unidades');
        
    // Módulo de Cargos (apenas para Coordenadores Adjuntos e Gerais)
    Route::resource('cargos', PositionController::class)
        ->except(['show'])
        ->middleware('permission:gerenciar cargos');
});