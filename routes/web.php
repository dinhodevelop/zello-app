<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ReceitaController;
use App\Http\Controllers\DespesaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\UserManagementController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', \App\Http\Middleware\EnsureUserHasHousehold::class])
    ->name('dashboard');

// Rotas protegidas por autenticação
Route::middleware(['auth', 'verified'])->group(function () {
    // Rotas básicas de Household (todos os usuários)
    Route::get('household/available', [HouseholdController::class, 'available'])->name('household.available');
    Route::post('household/{household}/join', [HouseholdController::class, 'join'])->name('household.join');
    Route::post('household/{household}/switch', [HouseholdController::class, 'switch'])->name('household.switch');
    Route::post('household/leave', [HouseholdController::class, 'leave'])->name('household.leave');
    
    // Rotas de Receitas (requer household)
    Route::resource('receitas', ReceitaController::class)
        ->middleware(\App\Http\Middleware\EnsureUserHasHousehold::class);
    
    // Rotas de Despesas (requer household)
    Route::resource('despesas', DespesaController::class)
        ->middleware(\App\Http\Middleware\EnsureUserHasHousehold::class);
});

// Rotas que requerem permissão de ADMIN
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    // Rotas de Gerenciamento de Household (apenas admin)
    Route::get('household', [HouseholdController::class, 'index'])->name('household.index');
    Route::get('household/create', [HouseholdController::class, 'create'])->name('household.create');
    Route::post('household', [HouseholdController::class, 'store'])->name('household.store');
    Route::get('household/{household}', [HouseholdController::class, 'show'])->name('household.show');
    Route::get('household/{household}/edit', [HouseholdController::class, 'edit'])->name('household.edit');
    Route::put('household/{household}', [HouseholdController::class, 'update'])->name('household.update');
    
    // Rotas de Gerenciamento de Usuários (apenas admin)
    Route::get('user-management', [UserManagementController::class, 'index'])->name('user-management.index');
    Route::get('user-management/create', [UserManagementController::class, 'create'])->name('user-management.create');
    Route::post('user-management', [UserManagementController::class, 'store'])->name('user-management.store');
    Route::post('user-management/{user}/add-to-household', [UserManagementController::class, 'addToHousehold'])->name('user-management.add-to-household');
    Route::post('user-management/{user}/remove-from-household', [UserManagementController::class, 'removeFromHousehold'])->name('user-management.remove-from-household');
    Route::post('user-management/{user}/move-to-household', [UserManagementController::class, 'moveToHousehold'])->name('user-management.move-to-household');
    Route::patch('user-management/{user}/role', [UserManagementController::class, 'updateRole'])->name('user-management.update-role');
    Route::delete('user-management/{user}', [UserManagementController::class, 'destroy'])->name('user-management.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
