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
    // Gestion des Photos
    Route::get('/admin/photos', [App\Http\Controllers\Admin\AdminPhotoController::class, 'index'])->name('admin.photos.index');
    Route::post('/admin/photos/import-zip', [App\Http\Controllers\Admin\AdminPhotoController::class, 'importZip'])->name('admin.photos.import_zip');
    Route::post('/admin/candidates/{user}/photo', [App\Http\Controllers\Admin\AdminPhotoController::class, 'updateManual'])->name('admin.candidates.photo.update');

    Route::get('/admin/candidates', [App\Http\Controllers\AdminCandidateController::class, 'index'])
        ->name('admin.candidates.index');
    Route::get('/admin/candidates/export', [App\Http\Controllers\AdminCandidateController::class, 'export'])
        ->name('admin.candidates.export');
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
    Route::put('/admin/candidates/{user}', [App\Http\Controllers\AdminCandidateController::class, 'update'])
        ->name('admin.candidates.update');
    
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

    Route::post('/admin/recherche-operationnelle/swap', [App\Http\Controllers\AdminOperationsResearchController::class, 'swapMembers'])
        ->name('admin.operations.swap');
    // Allow a safe GET redirect to the research page to avoid 405 when someone navigates
    // directly to the simulate URL. The actual simulation runner expects POST data.
    Route::get('/admin/recherche-operationnelle/simulate', function () {
        return redirect()->route('admin.operations.research')->with('info', 'Utilisez le formulaire de simulation pour lancer une prévisualisation.');
    })->name('admin.operations.simulate.get');

    Route::post('/admin/recherche-operationnelle/simulate', [App\Http\Controllers\AdminOperationsResearchController::class, 'simulateDistribute'])
        ->name('admin.operations.simulate');
    Route::post('/admin/recherche-operationnelle/update-draft', [App\Http\Controllers\AdminOperationsResearchController::class, 'updateDraftState'])
        ->name('admin.operations.update_draft');
    Route::post('/admin/recherche-operationnelle/discard-draft', [App\Http\Controllers\AdminOperationsResearchController::class, 'discardDraft'])
        ->name('admin.operations.discard_draft');
    Route::post('/admin/recherche-operationnelle/export-simulation', [App\Http\Controllers\AdminOperationsResearchController::class, 'exportSimulation'])
        ->name('admin.operations.export_simulation');
    Route::post('/admin/recherche-operationnelle/save-plan', [App\Http\Controllers\AdminOperationsResearchController::class, 'storePlan'])
        ->name('admin.operations.save_plan');
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
    // Plans de déploiement sauvegardés
    Route::get('/admin/deployment-plans', [App\Http\Controllers\AdminDeploymentPlanController::class, 'index'])
        ->name('admin.deployment_plans.index');
    Route::get('/admin/deployment-plans/{plan}/download', [App\Http\Controllers\AdminDeploymentPlanController::class, 'download'])
        ->name('admin.deployment_plans.download');
    Route::delete('/admin/deployment-plans/{plan}', [App\Http\Controllers\AdminDeploymentPlanController::class, 'destroy'])
        ->name('admin.deployment_plans.destroy');

    // Synchronisation Liste Maître
    Route::get('/admin/master-sync', [App\Http\Controllers\AdminMasterSyncController::class, 'index'])
        ->name('admin.master_sync.index');
    Route::post('/admin/master-sync', [App\Http\Controllers\AdminMasterSyncController::class, 'sync'])
        ->name('admin.master_sync.process');
    Route::post('/admin/master-sync/confirm', [App\Http\Controllers\AdminMasterSyncController::class, 'confirm'])
        ->name('admin.master_sync.confirm');
    Route::get('/admin/master-sync/cancel', [App\Http\Controllers\AdminMasterSyncController::class, 'cancel'])
        ->name('admin.master_sync.cancel');
    Route::post('/admin/master-sync/reset', [App\Http\Controllers\AdminMasterSyncController::class, 'reset'])
        ->name('admin.master_sync.reset');

    // Gestion des Quiz QCM
    Route::get('/admin/quizzes', [App\Http\Controllers\AdminQuizController::class, 'index'])->name('admin.quizzes.index');
    Route::get('/admin/quizzes/results', [App\Http\Controllers\AdminQuizController::class, 'results'])->name('admin.quizzes.results');
    Route::post('/admin/quizzes/settings', [App\Http\Controllers\AdminQuizController::class, 'updateSettings'])->name('admin.quizzes.settings.update');
    Route::get('/admin/quizzes/create', [App\Http\Controllers\AdminQuizController::class, 'create'])->name('admin.quizzes.create');
    Route::post('/admin/quizzes', [App\Http\Controllers\AdminQuizController::class, 'store'])->name('admin.quizzes.store');
    Route::get('/admin/quizzes/{quiz}', [App\Http\Controllers\AdminQuizController::class, 'show'])->name('admin.quizzes.show');
    Route::get('/admin/quizzes/{quiz}/edit', [App\Http\Controllers\AdminQuizController::class, 'edit'])->name('admin.quizzes.edit');
    Route::put('/admin/quizzes/{quiz}', [App\Http\Controllers\AdminQuizController::class, 'update'])->name('admin.quizzes.update');
    Route::delete('/admin/quizzes/{quiz}', [App\Http\Controllers\AdminQuizController::class, 'destroy'])->name('admin.quizzes.destroy');
    Route::post('/admin/quizzes/{quiz}/toggle', [App\Http\Controllers\AdminQuizController::class, 'toggle'])->name('admin.quizzes.toggle');

    // Gestion des Questions & Options (Smart Builder)
    Route::post('/admin/quizzes/{quiz}/questions', [App\Http\Controllers\AdminQuizController::class, 'storeQuestion'])->name('admin.quizzes.questions.store');
    Route::put('/admin/quiz-questions/{question}', [App\Http\Controllers\AdminQuizController::class, 'updateQuestion'])->name('admin.questions.update');
    Route::delete('/admin/quiz-questions/{question}', [App\Http\Controllers\AdminQuizController::class, 'destroyQuestion'])->name('admin.questions.destroy');

    Route::get('/admin/questions', [App\Http\Controllers\AdminDynamicQuestionController::class, 'index'])->name('admin.questions.index');

    Route::put('/admin/questions/{question}', [App\Http\Controllers\AdminDynamicQuestionController::class, 'update'])->name('admin.questions.update');
    Route::post('/admin/questions/{question}/toggle', [App\Http\Controllers\AdminDynamicQuestionController::class, 'toggle'])->name('admin.questions.toggle');
    Route::post('/admin/questions/{question}/order', [App\Http\Controllers\AdminDynamicQuestionController::class, 'updateOrder'])->name('admin.questions.order');
});

// Routes QCM Agent
Route::get('/qcm/{slug}', [App\Http\Controllers\AuthQuizController::class, 'showLoginForm'])->name('qcm.login');
Route::post('/qcm/{slug}/login', [App\Http\Controllers\AuthQuizController::class, 'login'])->name('qcm.login.submit');
Route::post('/qcm/logout', [App\Http\Controllers\AuthQuizController::class, 'logout'])->name('qcm.logout');

Route::middleware(['web', 'quiz.auth'])->group(function () {
    Route::get('/evaluation/{slug}', [App\Http\Controllers\AuthQuizController::class, 'showQuiz'])->name('qcm.show');
    Route::post('/evaluation/{slug}/submit', [App\Http\Controllers\AuthQuizController::class, 'submitQuiz'])->name('qcm.submit');
});
