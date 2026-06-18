<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Quiz;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthQuizController extends Controller
{
    /**
     * Affiche la page de connexion au QCM.
     */
    public function showLoginForm($slug)
    {
        $quiz = Quiz::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('qcm.login', compact('quiz'));
    }

    /**
     * Gère l'authentification flash de l'agent.
     */
    public function login(Request $request, $slug)
    {
        $quiz = Quiz::where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        // 1. Vérifier le mot de passe de formation
        $trainingPassword = Setting::getValue('training_default_password', 'Formation2026');
        if ($request->password !== $trainingPassword) {
            return back()->withErrors(['password' => 'Le mot de passe de formation est incorrect.'])->withInput();
        }

        // 2. Vérifier si l'agent existe par Matricule ou Téléphone
        $user = User::where('matricule', $request->identifier)
                    ->orWhere('telephone', $request->identifier)
                    ->first();

        if (!$user) {
            // Cas 1 : Agent inconnu (hors base)
            return back()->with('show_registration_modal', true)
                         ->with('is_unknown', true)
                         ->with('user_matricule', $request->identifier);
        }

        // 3. Vérifier le statut d'inscription (Garde-fou pour officiel_attente)
        if ($user->validation_status === 'officiel_attente' || is_null($user->validation_status)) {
            // Cas 2 : Agent reconnu mais profil incomplet
            return back()->with('show_registration_modal', true)
                         ->with('is_unknown', false)
                         ->with('user_matricule', $user->matricule);
        }

        // 4. Vérifier si le profil de l'agent correspond au ciblage du quiz
        if ($quiz->profils->isNotEmpty() && !$quiz->profils->contains($user->profil_id)) {
            return back()->withErrors(['identifier' => 'Ce quiz n\'est pas destiné à votre profil.'])->withInput();
        }

        // 5. Authentifier pour la session QCM
        Session::put('quiz_auth_user_id', $user->id);
        Session::put('quiz_auth_slug', $quiz->slug);

        return redirect()->route('qcm.show', $quiz->slug);
    }

    /**
     * Déconnexion de la session QCM.
     */
    public function logout()
    {
        Session::forget(['quiz_auth_user_id', 'quiz_auth_slug']);
        return redirect()->url('/');
    }

    /**
     * Affiche le formulaire du Quiz.
     */
    public function showQuiz($slug)
    {
        $quiz = Quiz::where('slug', $slug)->with('questions.options')->firstOrFail();
        $user = User::findOrFail(Session::get('quiz_auth_user_id'));

        // Vérifier si l'agent a déjà passé ce test
        $result = \App\Models\QuizResult::where('user_id', $user->id)->where('quiz_id', $quiz->id)->first();
        if ($result) {
            // Calculer le total des points possible pour le quiz
            $totalPoints = $quiz->questions->sum('points');
            return view('qcm.thanks', [
                'quiz' => $quiz, 
                'alreadyDone' => true, 
                'score' => $result->score,
                'totalPoints' => $totalPoints
            ]);
        }

        return view('qcm.show', compact('quiz', 'user'));
    }

    /**
     * Traite les réponses et calcule la note.
     */
    public function submitQuiz(Request $request, $slug)
    {
        $quiz = Quiz::where('slug', $slug)->with('questions.options')->firstOrFail();
        $user = User::findOrFail(Session::get('quiz_auth_user_id'));

        // Sécurité double soumission
        if (\App\Models\QuizResult::where('user_id', $user->id)->where('quiz_id', $quiz->id)->exists()) {
            return redirect()->route('qcm.login', $slug);
        }

        $answers = $request->input('answers', []);
        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $userAnswer = $answers[$question->id] ?? null;

            if ($question->type === 'unique') {
                // Choix unique : l'ID de l'option doit être celui de la bonne réponse
                $correctOption = $question->options->where('is_correct', true)->first();
                if ($correctOption && $userAnswer == $correctOption->id) {
                    $earnedPoints += $question->points;
                }
            } else {
                // Choix multiples : toutes les bonnes réponses doivent être cochées ET aucune mauvaise
                $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->toArray();
                $userAnswerIds = is_array($userAnswer) ? array_map('intval', $userAnswer) : [];
                
                sort($correctOptionIds);
                sort($userAnswerIds);

                if ($correctOptionIds === $userAnswerIds) {
                    $earnedPoints += $question->points;
                }
            }
        }

        // Enregistrer le résultat
        \App\Models\QuizResult::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => $earnedPoints,
            'answers_json' => $answers,
        ]);

        return view('qcm.thanks', [
            'quiz' => $quiz,
            'score' => $earnedPoints,
            'totalPoints' => $totalPoints
        ]);
    }
}
