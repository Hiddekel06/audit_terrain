<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserRegionChoice;
use App\Models\Region;
use App\Models\Motivation;
use App\Models\UserDynamicAnswer;
use App\Models\UserRegionChoiceMotivation;

class UserRegionChoiceController extends Controller
{
    /**
     * Affiche la question simple de confirmation avant de choisir les régions.
     */
    public function decision()
    {
        if (!session()->has('pending_user_payload')) {
            return redirect()->route('utilisateur.form')->withErrors([
                'user' => 'Veuillez d\'abord remplir le formulaire d\'identification.',
            ]);
        }

        return view('choix_deploiement');
    }

    /**
     * Enregistre la réponse Oui/Non à la disponibilité nationale.
     */
    public function decisionStore(Request $request)
    {
        $validated = $request->validate([
            'ready_to_deploy' => ['required', 'in:yes,no'],
        ]);

        if ($validated['ready_to_deploy'] === 'yes') {
            session(['ready_to_deploy_all_regions' => true]);
            $this->persistPendingUser();

            session()->forget([
                'pending_user_payload',
                'pending_dynamic_answers',
                'user_id',
                'region_choice_mode',
                'ready_to_deploy_all_regions',
            ]);

            return view('merci');
        }

        session(['ready_to_deploy_all_regions' => false]);
        session(['region_choice_mode' => 'single']);

        return redirect()->route('user_region_choice.create');
    }

    /**
     * Affiche le formulaire de choix des régions pour l'utilisateur.
     */
    public function create()
    {
        if (!session()->has('pending_user_payload')) {
            return redirect()->route('utilisateur.form')->withErrors([
                'user' => 'Veuillez d\'abord remplir le formulaire d\'identification.',
            ]);
        }

        $regions = Region::all();
        $motivations = Motivation::orderBy('libelle')->get();
        $singleRegionSelection = session('region_choice_mode') === 'single';

        return view('choix_regions', compact('regions', 'motivations', 'singleRegionSelection'));
    }

    /**
     * Enregistre les choix de régions de l'utilisateur.
     */
    public function store(Request $request)
    {
        $singleRegionSelection = session('region_choice_mode') === 'single';

        $rules = $singleRegionSelection
            ? [
                'choix_1' => 'required|exists:regions,id',
                'motivation_autre_1' => 'nullable|string|max:100',
            ]
            : [
                'choix_1' => 'required|different:choix_2,choix_3|exists:regions,id',
                'choix_2' => 'required|different:choix_1,choix_3|exists:regions,id',
                'choix_3' => 'required|different:choix_1,choix_2|exists:regions,id',
                'motivation_autre_1' => 'nullable|string|max:100',
                'motivation_autre_2' => 'nullable|string|max:100',
                'motivation_autre_3' => 'nullable|string|max:100',
            ];

        $request->validate($rules);

        $pendingUser = session('pending_user_payload');
        $pendingAnswers = session('pending_dynamic_answers', []);

        if (!$pendingUser) {
            return redirect()->route('utilisateur.form')->withErrors(['user' => 'Utilisateur introuvable. Veuillez vous identifier.']);
        }

        $user = $this->persistPendingUser($pendingUser, $pendingAnswers);

        DB::transaction(function () use ($request, $user, $singleRegionSelection) {
            $regionIndexes = $singleRegionSelection ? [1] : [1, 2, 3];

            foreach ($regionIndexes as $i) {
                $regionChoice = UserRegionChoice::create([
                    'user_id' => $user->id,
                    'region_id' => $request->input('choix_' . $i),
                    'ordre' => $i,
                ]);

                // Motivations types (checkbox)
                $motivationIds = $request->input('motivations_' . $i, []);
                foreach ($motivationIds as $motivationId) {
                    UserRegionChoiceMotivation::create([
                        'user_region_choice_id' => $regionChoice->id,
                        'motivation_id' => $motivationId,
                        'motivation_libre' => null,
                    ]);
                }

                // Motivation personnalisée facultative
                $motivationLibre = trim((string) $request->input('motivation_autre_' . $i, ''));
                if ($motivationLibre !== '') {
                    UserRegionChoiceMotivation::create([
                        'user_region_choice_id' => $regionChoice->id,
                        'motivation_id' => null,
                        'motivation_libre' => $motivationLibre,
                    ]);
                }
            }
        });

        session()->forget([
            'pending_user_payload',
            'pending_dynamic_answers',
            'user_id',
            'region_choice_mode',
            'ready_to_deploy_all_regions',
        ]);

        return view('merci');
    }

    /**
     * Crée ou met à jour le candidat à partir des données conservées en session.
     */
    private function persistPendingUser(?array $pendingUser = null, ?array $pendingAnswers = null): User
    {
        $pendingUser = $pendingUser ?? session('pending_user_payload');
        $pendingAnswers = $pendingAnswers ?? session('pending_dynamic_answers', []);

        return DB::transaction(function () use ($pendingUser, $pendingAnswers) {
            $data = [
                'prenom' => $pendingUser['prenom'],
                'nom' => $pendingUser['nom'],
                'telephone' => $pendingUser['telephone'] ?? null,
                'email' => $pendingUser['email'] ?? null,
                'disponibilite' => $pendingUser['disponibilite'] ?? 'immediate',
                'matricule' => $pendingUser['matricule'],
                'profil_id' => $pendingUser['profil_id'] ?? null,
                'niveau_numerique' => $pendingUser['niveau_numerique'] ?? null,
                'experiences' => $pendingUser['experiences'] ?? null,
                'competences_techniques' => $pendingUser['competences_techniques'] ?? null,
                'ministere_id' => $pendingUser['ministere_id'] ?? null,
                'direction' => $pendingUser['direction'] ?? null,
                'ready_to_deploy_all_regions' => session('ready_to_deploy_all_regions', false),
            ];

            // Si c'est une réconciliation (l'utilisateur existe déjà via import)
            if (!empty($pendingUser['id'])) {
                $user = User::find($pendingUser['id']);
                
                // On met à jour le statut : s'il était officiel_attente, il devient officiel_inscrit
                if ($user->validation_status === 'officiel_attente') {
                    $data['validation_status'] = 'officiel_inscrit';
                }
                
                // On sauvegarde le profil initial s'il change
                if ($user->profil_id != $data['profil_id'] && !$user->profil_initial_id) {
                    $data['profil_initial_id'] = $user->profil_id;
                }

                $user->update($data);
            } else {
                // Création classique (nouveau venu)
                $data['profil_initial_id'] = $pendingUser['profil_id'] ?? null;
                $data['source_type'] = 'manual';
                $user = User::create($data);
            }

            // Nettoyage des anciennes réponses si réconciliation
            UserDynamicAnswer::where('user_id', $user->id)->delete();

            foreach ($pendingAnswers as $answer) {
                UserDynamicAnswer::create([
                    'user_id' => $user->id,
                    'dynamic_question_id' => $answer['dynamic_question_id'],
                    'dynamic_question_option_id' => $answer['dynamic_question_option_id'] ?? null,
                    'answer_text' => $answer['answer_text'] ?? null,
                ]);
            }

            return $user;
        });
    }
}
