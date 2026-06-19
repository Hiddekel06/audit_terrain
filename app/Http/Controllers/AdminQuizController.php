<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminQuizController extends Controller
{
    /**
     * Liste des Quiz (Affichage par cartes).
     */
    public function index()
    {
        $quizzes = Quiz::with(['profils'])->withCount('questions')->orderBy('created_at', 'desc')->get();
        $trainingPassword = \App\Models\Setting::getValue('training_default_password', 'Formation2026');
        
        return view('admin.quizzes.index', compact('quizzes', 'trainingPassword'));
    }

    /**
     * Liste des résultats des agents.
     */
    public function results(Request $request)
    {
        $query = \App\Models\QuizResult::with(['user.profil', 'user.ministere', 'quiz.questions.options']);

        // Filtre par Recherche (Nom ou Matricule)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%");
            });
        }

        // Filtre par Quiz
        if ($request->filled('quiz_id')) {
            $query->where('quiz_id', $request->quiz_id);
        }

        // Filtre par Profil
        if ($request->filled('profil_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('profil_id', $request->profil_id);
            });
        }
        
        if ($request->filled('score_order')) {
            $query->orderBy('score', $request->score_order);
        } else {
            $query->latest();
        }

        $selectedQuestion = null;
        if ($request->filled('question_id')) {
            $selectedQuestion = \App\Models\QuizQuestion::with('options')->find($request->question_id);
            if ($selectedQuestion) {
                $query->where('quiz_id', $selectedQuestion->quiz_id);
            }
        }

        $results = $query->orderBy('created_at', 'desc')->get();

        // Filtre par Statut de réponse (Correct / Incorrect)
        if ($selectedQuestion && $request->filled('question_status')) {
            $status = $request->question_status;

            $results = $results->filter(function($result) use ($selectedQuestion, $status) {
                if ($result->quiz_id !== $selectedQuestion->quiz_id) {
                    return false;
                }

                $correctOptionIds = $selectedQuestion->options->where('is_correct', true)->pluck('id')->toArray();
                $userAns = $result->answers_json[$selectedQuestion->id] ?? null;
                $userAnsIds = is_array($userAns) ? array_map('intval', $userAns) : ($userAns !== null ? [intval($userAns)] : []);

                sort($correctOptionIds);
                sort($userAnsIds);

                $isCorrect = ($correctOptionIds === $userAnsIds);

                if ($status === 'correct') {
                    return $isCorrect;
                } elseif ($status === 'incorrect') {
                    return !$isCorrect;
                }

                return true;
            });
        }

        if ($request->boolean('export')) {
            $filename = 'resultats_evaluations_' . now()->format('Ymd_His') . '.xlsx';
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\QuizResultsExport($results, $selectedQuestion), $filename);
        }
        
        $quizzes = Quiz::with('questions.options')->orderBy('titre')->get();
        $profils = Profil::orderBy('libelle')->get();

        return view('admin.quizzes.results', compact('results', 'quizzes', 'profils', 'selectedQuestion'));
    }

    /**
     * Met à jour le mot de passe de formation global.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'training_default_password' => 'required|string|max:50',
        ]);

        \App\Models\Setting::updateOrCreate(
            ['key' => 'training_default_password'],
            ['value' => $validated['training_default_password']]
        );

        return back()->with('success', 'Le mot de passe de formation a été mis à jour.');
    }

    /**
     * Formulaire de création d'un Quiz.
     */
    public function create()
    {
        $profils = Profil::where('is_active', true)->orderBy('ordre')->get();
        return view('admin.quizzes.create', compact('profils'));
    }

    /**
     * Enregistrement d'un nouveau Quiz.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'profil_ids' => 'nullable|array',
            'profil_ids.*' => 'exists:profil,id',
        ]);

        $quiz = \DB::transaction(function () use ($validated, $request) {
            $quiz = Quiz::create([
                'titre' => $validated['titre'],
                'description' => $validated['description'] ?? null,
                'slug' => Str::slug($validated['titre']) . '-' . rand(1000, 9999),
                'is_active' => $request->boolean('is_active'),
            ]);

            if (!empty($validated['profil_ids'])) {
                $quiz->profils()->sync($validated['profil_ids']);
            }

            return $quiz;
        });

        return redirect()->route('admin.quizzes.show', $quiz)->with('success', 'Le Quiz a été créé. Vous pouvez maintenant ajouter les questions.');
    }

    /**
     * Formulaire de modification d'un Quiz.
     */
    public function edit(Quiz $quiz)
    {
        $quiz->load('profils');
        $profils = Profil::where('is_active', true)->orderBy('ordre')->get();
        $selectedProfils = $quiz->profils->pluck('id')->toArray();
        
        return view('admin.quizzes.edit', compact('quiz', 'profils', 'selectedProfils'));
    }

    /**
     * Mise à jour des informations d'un Quiz.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'profil_ids' => 'nullable|array',
            'profil_ids.*' => 'exists:profil,id',
        ]);

        \DB::transaction(function () use ($quiz, $validated, $request) {
            $quiz->update([
                'titre' => $validated['titre'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);

            if (isset($validated['profil_ids'])) {
                $quiz->profils()->sync($validated['profil_ids']);
            } else {
                $quiz->profils()->detach();
            }
        });

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz mis à jour avec succès.');
    }

    /**
     * Affichage d'un Quiz (Détails et Questions).
     */
    public function show(Quiz $quiz)
    {
        $quiz->load(['sections.questions.options', 'profils']);

        // 1. Récupérer les IDs des profils ciblés
        $targetProfilIds = $quiz->profils->pluck('id')->toArray();

        // 2. Récupérer TOUS les agents inscrits correspondant à ces profils (indépendamment du statut officiel/réserve)
        $queryAgents = \App\Models\User::query();
        if (!empty($targetProfilIds)) {
            $queryAgents->whereIn('profil_id', $targetProfilIds);
        }
        
        // On récupère tous ceux qui ont un profil (donc inscrits/validés au sens large)
        $targetAgents = $queryAgents->whereNotNull('profil_id')->get();

        // 3. Récupérer les IDs des agents ayant déjà répondu
        $respondedUserIds = $quiz->results->pluck('user_id')->toArray();

        // 4. Séparer en deux listes
        $stats = [
            'total_target' => $targetAgents->count(),
            'total_responded' => count($respondedUserIds),
            'responded' => [],
            'pending' => []
        ];

        foreach ($targetAgents as $agent) {
            if (in_array($agent->id, $respondedUserIds)) {
                $stats['responded'][] = $agent;
            } else {
                $stats['pending'][] = $agent;
            }
        }

        return view('admin.quizzes.show', compact('quiz', 'stats'));
    }

    /**
     * Enregistre une nouvelle section dans le Quiz.
     */
    public function storeSection(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $quiz->sections()->create([
            'titre' => $validated['titre'],
            'description' => $validated['description'],
            'ordre' => $quiz->sections()->count() + 1,
        ]);

        return back()->with('success', 'Section ajoutée avec succès.');
    }

    /**
     * Met à jour une section.
     */
    public function updateSection(Request $request, \App\Models\QuizSection $section)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $section->update($validated);

        return back()->with('success', 'Section mise à jour.');
    }

    /**
     * Supprime une section.
     */
    public function destroySection(\App\Models\QuizSection $section)
    {
        // On ne peut supprimer une section que si elle est vide ou si on déplace les questions
        if ($section->questions()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une section qui contient des questions. Déplacez les questions d\'abord.');
        }

        $section->delete();
        return back()->with('success', 'Section supprimée.');
    }

    /**
     * Activation / Désactivation d'un Quiz.
     */
    public function toggle(Quiz $quiz)
    {
        $quiz->is_active = !$quiz->is_active;
        $quiz->save();

        return back()->with('success', 'Le statut du Quiz a été mis à jour.');
    }

    /**
     * Enregistre une question et ses options.
     */
    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:500',
            'type' => 'required|in:unique,multiple',
            'points' => 'required|integer|min:0',
            'section_id' => 'required|exists:quiz_sections,id',
            'options' => 'required|array|min:2',
            'options.*.libelle' => 'required|string|max:255',
            'options.*.is_correct' => 'nullable',
        ]);

        // Vérifier qu'au moins une option est correcte
        $hasCorrect = collect($request->options)->contains(fn($opt) => isset($opt['is_correct']));
        if (!$hasCorrect) {
            return back()->withErrors(['options' => 'Vous devez cocher au moins une réponse juste.'])->withInput();
        }

        \DB::transaction(function () use ($quiz, $validated) {
            $question = $quiz->questions()->create([
                'libelle' => $validated['libelle'],
                'type' => $validated['type'],
                'points' => $validated['points'],
                'section_id' => $validated['section_id'],
                'ordre' => $quiz->questions()->count() + 1,
            ]);

            foreach ($validated['options'] as $optData) {
                $question->options()->create([
                    'libelle' => $optData['libelle'],
                    'is_correct' => isset($optData['is_correct']),
                ]);
            }
        });

        return back()->with('success', 'Question ajoutée avec succès.');
    }

    /**
     * Met à jour une question et ses options.
     */
    public function updateQuestion(Request $request, \App\Models\QuizQuestion $question)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:500',
            'type' => 'required|in:unique,multiple',
            'points' => 'required|integer|min:0',
            'section_id' => 'required|exists:quiz_sections,id',
            'options' => 'required|array|min:2',
            'options.*.libelle' => 'required|string|max:255',
            'options.*.is_correct' => 'nullable',
        ]);

        // Vérifier qu'au moins une option est correcte
        $hasCorrect = collect($request->options)->contains(fn($opt) => isset($opt['is_correct']));
        if (!$hasCorrect) {
            return back()->withErrors(['options' => 'Vous devez cocher au moins une réponse juste.'])->withInput();
        }

        \DB::transaction(function () use ($question, $validated) {
            $question->update([
                'libelle' => $validated['libelle'],
                'type' => $validated['type'],
                'points' => $validated['points'],
                'section_id' => $validated['section_id'],
            ]);

            // On supprime les anciennes options et on recrée
            $question->options()->delete();

            foreach ($validated['options'] as $optData) {
                $question->options()->create([
                    'libelle' => $optData['libelle'],
                    'is_correct' => isset($optData['is_correct']),
                ]);
            }
        });

        return back()->with('success', 'Question mise à jour.');
    }

    /**
     * Supprime une question.
     */
    public function destroyQuestion(\App\Models\QuizQuestion $question)
    {
        $question->delete();
        return back()->with('success', 'Question supprimée.');
    }

    /**
     * Suppression d'un Quiz.
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz supprimé.');
    }
}
