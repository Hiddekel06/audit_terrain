<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome_profiles');
});

// Affichage du formulaire utilisateur
Route::get('/utilisateur', [App\Http\Controllers\UserController::class, 'create'])->name('utilisateur.form');

// Route pour la soumission du formulaire utilisateur
Route::post('/utilisateur', [App\Http\Controllers\UserController::class, 'store'])->name('utilisateur.store');

// Choix des régions (affichage et enregistrement)
Route::get('/choix-regions', [App\Http\Controllers\UserRegionChoiceController::class, 'create'])->name('user_region_choice.create');
Route::post('/choix-regions', [App\Http\Controllers\UserRegionChoiceController::class, 'store'])->name('user_region_choice.store');

// Auth admin
Route::get('/admin/login', [App\Http\Controllers\AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [App\Http\Controllers\AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [App\Http\Controllers\AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware('admin.auth')->group(function () {
    // Dashboard admin (toutes les stats)
    Route::get('/admin/dashboard', [App\Http\Controllers\AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    // Recherche operationnelle (admin)
    Route::view('/admin/recherche-operationnelle', 'admin.operations-research')
        ->name('admin.operations.research');

    // Vue admin des utilisateurs par région et ordre de priorité
    Route::get('/admin/regions-priorites', [App\Http\Controllers\AdminRegionPriorityController::class, 'index'])
        ->name('admin.regions.priorities');
    Route::get('/admin/regions-priorites/{region}', [App\Http\Controllers\AdminRegionPriorityController::class, 'show'])
        ->name('admin.regions.priorities.show');

    // Ajout / gestion des motivations (admin)
    Route::post('/admin/motivations', [App\Http\Controllers\MotivationController::class, 'store'])->name('admin.motivations.store');
    Route::delete('/admin/motivations/{motivation}', [App\Http\Controllers\MotivationController::class, 'destroy'])->name('admin.motivations.destroy');
    Route::post('/admin/motivations/{motivation}/restore', [App\Http\Controllers\MotivationController::class, 'restore'])->name('admin.motivations.restore');

    // Questions dynamiques (admin)
    Route::post('/admin/questions', [App\Http\Controllers\AdminDynamicQuestionController::class, 'store'])->name('admin.questions.store');
    Route::put('/admin/questions/{question}', [App\Http\Controllers\AdminDynamicQuestionController::class, 'update'])->name('admin.questions.update');
    Route::post('/admin/questions/{question}/toggle', [App\Http\Controllers\AdminDynamicQuestionController::class, 'toggle'])->name('admin.questions.toggle');
    Route::post('/admin/questions/{question}/order', [App\Http\Controllers\AdminDynamicQuestionController::class, 'updateOrder'])->name('admin.questions.order');
});
