<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ReceitaController;
use App\Http\Controllers\DespesaController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rotas protegidas por autenticação
Route::middleware(['auth', 'verified'])->group(function () {
    // Rotas de Receitas
    Route::resource('receitas', ReceitaController::class);
    
    // Rotas de Despesas
    Route::resource('despesas', DespesaController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
