<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Affichage du formulaire utilisateur
Route::get('/utilisateur', function () {
    return view('utilisateur_form');
})->name('utilisateur.form');

// Route pour la soumission du formulaire utilisateur
Route::post('/utilisateur', [App\Http\Controllers\UserController::class, 'store'])->name('utilisateur.store');

// Choix des régions (affichage et enregistrement)
Route::get('/choix-regions', [App\Http\Controllers\UserRegionChoiceController::class, 'create'])->name('user_region_choice.create');
Route::post('/choix-regions', [App\Http\Controllers\UserRegionChoiceController::class, 'store'])->name('user_region_choice.store');
