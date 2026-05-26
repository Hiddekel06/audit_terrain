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
Route::get('/choix-deploiement', [App\Http\Controllers\UserRegionChoiceController::class, 'decision'])->name('user_region_choice.decision');
Route::post('/choix-deploiement', [App\Http\Controllers\UserRegionChoiceController::class, 'decisionStore'])->name('user_region_choice.decision.store');
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

    // Gestion candidats
    Route::get('/admin/candidates', [App\Http\Controllers\AdminCandidateController::class, 'index'])
        ->name('admin.candidates.index');
    Route::get('/admin/candidates/create', [App\Http\Controllers\AdminCandidateController::class, 'create'])
        ->name('admin.candidates.create');
    Route::get('/admin/candidates/check-matricule', [App\Http\Controllers\AdminCandidateController::class, 'checkMatricule'])
        ->name('admin.candidates.check_matricule');
    Route::post('/admin/candidates', [App\Http\Controllers\AdminCandidateController::class, 'store'])
        ->name('admin.candidates.store');
    Route::post('/admin/candidates/import', [App\Http\Controllers\AdminCandidateController::class, 'importExcel'])
        ->name('admin.candidates.import');
    Route::post('/admin/candidates/import/confirm', [App\Http\Controllers\AdminCandidateController::class, 'confirmImport'])
        ->name('admin.candidates.import.confirm');
    Route::post('/admin/candidates/import/cancel', [App\Http\Controllers\AdminCandidateController::class, 'cancelImport'])
        ->name('admin.candidates.import.cancel');
    Route::get('/admin/candidates/template', [App\Http\Controllers\AdminCandidateController::class, 'downloadTemplate'])
        ->name('admin.candidates.template');
    Route::get('/admin/candidates/{user}', [App\Http\Controllers\AdminCandidateController::class, 'show'])
        ->name('admin.candidates.show');
    Route::delete('/admin/candidates/{user}', [App\Http\Controllers\AdminCandidateController::class, 'destroy'])
        ->name('admin.candidates.destroy');
    Route::get('/admin/candidates/profil/{profil}', [App\Http\Controllers\AdminCandidateController::class, 'profilDetail'])
        ->name('admin.candidates.profil');

    // Recherche operationnelle (admin)
    Route::get('/admin/recherche-operationnelle', [App\Http\Controllers\AdminOperationsResearchController::class, 'index'])
        ->name('admin.operations.research');
    Route::post('/admin/recherche-operationnelle/team', [App\Http\Controllers\AdminOperationsResearchController::class, 'storeTeam'])
        ->name('admin.operations.team.store');
    Route::post('/admin/recherche-operationnelle/assign', [App\Http\Controllers\AdminOperationsResearchController::class, 'assignMember'])
        ->name('admin.operations.assign');
    // Allow a safe GET redirect to the research page to avoid 405 when someone navigates
    // directly to the simulate URL. The actual simulation runner expects POST data.
    Route::get('/admin/recherche-operationnelle/simulate', function () {
        return redirect()->route('admin.operations.research')->with('info', 'Utilisez le formulaire de simulation pour lancer une prévisualisation.');
    })->name('admin.operations.simulate.get');

    Route::post('/admin/recherche-operationnelle/simulate', [App\Http\Controllers\AdminOperationsResearchController::class, 'simulateDistribute'])
        ->name('admin.operations.simulate');
    Route::get('/admin/recherche-operationnelle/optimize', function () {
        return redirect()->route('admin.operations.research')->with('info', 'L’optimisation automatique se lance depuis la modale de déploiement.');
    })->name('admin.operations.optimize.get');
    Route::post('/admin/recherche-operationnelle/optimize', [App\Http\Controllers\AdminOperationsResearchController::class, 'optimizeDistribute'])
        ->name('admin.operations.optimize');
    Route::post('/admin/recherche-operationnelle/profile', [App\Http\Controllers\AdminOperationsResearchController::class, 'updateProfile'])
        ->name('admin.operations.profile.update');
    Route::post('/admin/recherche-operationnelle/auto', [App\Http\Controllers\AdminOperationsResearchController::class, 'autoDistribute'])
        ->name('admin.operations.auto');
    Route::post('/admin/recherche-operationnelle/reset', [App\Http\Controllers\AdminOperationsResearchController::class, 'resetDeployment'])
        ->name('admin.operations.reset');
    Route::delete('/admin/recherche-operationnelle/team/{team}', [App\Http\Controllers\AdminOperationsResearchController::class, 'destroyTeam'])
        ->name('admin.operations.team.destroy');

    // Vue admin des utilisateurs par région et ordre de priorité
    Route::get('/admin/regions-priorites', [App\Http\Controllers\AdminRegionPriorityController::class, 'index'])
        ->name('admin.regions.priorities');
    Route::get('/admin/regions-priorites/{region}', [App\Http\Controllers\AdminRegionPriorityController::class, 'show'])
        ->name('admin.regions.priorities.show');

    // Vue admin de la répartition des agents par ministère
    Route::get('/admin/ministeres', [App\Http\Controllers\AdminMinistereStatsController::class, 'index'])
        ->name('admin.ministeres.index');

    // Ajout / gestion des motivations (admin)
    Route::post('/admin/motivations', [App\Http\Controllers\MotivationController::class, 'store'])->name('admin.motivations.store');
    Route::delete('/admin/motivations/{motivation}', [App\Http\Controllers\MotivationController::class, 'destroy'])->name('admin.motivations.destroy');
    Route::post('/admin/motivations/{motivation}/restore', [App\Http\Controllers\MotivationController::class, 'restore'])->name('admin.motivations.restore');

    // Import reports (skipped rows CSVs)
    Route::get('/admin/import-reports', [App\Http\Controllers\AdminImportReportsController::class, 'index'])
        ->name('admin.import_reports.index');
    Route::get('/admin/import-reports/download/{filename}', [App\Http\Controllers\AdminImportReportsController::class, 'download'])
        ->name('admin.import_reports.download');

    // Questions dynamiques (admin)
    Route::post('/admin/questions', [App\Http\Controllers\AdminDynamicQuestionController::class, 'store'])->name('admin.questions.store');
    Route::put('/admin/questions/{question}', [App\Http\Controllers\AdminDynamicQuestionController::class, 'update'])->name('admin.questions.update');
    Route::post('/admin/questions/{question}/toggle', [App\Http\Controllers\AdminDynamicQuestionController::class, 'toggle'])->name('admin.questions.toggle');
    Route::post('/admin/questions/{question}/order', [App\Http\Controllers\AdminDynamicQuestionController::class, 'updateOrder'])->name('admin.questions.order');
});
