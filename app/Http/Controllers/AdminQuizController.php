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
    public function results()
    {
        $results = \App\Models\QuizResult::with(['user', 'quiz.questions.options'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.quizzes.results', compact('results'));
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
     * Affichage d'un Quiz (Détails et Questions).
     */
    public function show(Quiz $quiz)
    {
        $quiz->load(['questions.options', 'profils']);
        return view('admin.quizzes.show', compact('quiz'));
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
            'options' => 'required|array|min:2',
            'options.*.libelle' => 'required|string|max:255',
            'options.*.is_correct' => 'nullable',
        ]);

        \DB::transaction(function () use ($quiz, $validated) {
            $question = $quiz->questions()->create([
                'libelle' => $validated['libelle'],
                'type' => $validated['type'],
                'points' => $validated['points'],
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
