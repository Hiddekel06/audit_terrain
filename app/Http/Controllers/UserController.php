<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profil;
use App\Models\Ministere;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Affiche le formulaire utilisateur avec les questions dynamiques actives.
     */
    public function create(Request $request)
    {
        $profils = Profil::where('is_active', true)
            ->where('code', '!=', 'chauffeur')
            ->orderBy('ordre')
            ->orderBy('id')
            ->get();

        $ministeres = Ministere::orderBy('nom')->get();

        $prefilledMatricule = $request->query('matricule');

        $selectedProfile = null;
        if ($request->has('profil_id')) {
            $selectedProfile = $profils->firstWhere('id', $request->profil_id);
        }

        return view('utilisateur_form', compact('profils', 'ministeres', 'selectedProfile', 'prefilledMatricule'));
    }

    /**
     * Enregistre temporairement les données utilisateur en session.
     * Redirige ensuite vers le questionnaire ou le choix de région.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|max:150',
            'ministere_id' => 'required|exists:ministeres,id',
            'direction' => 'required|string|max:255',
            'profil_id' => 'required|exists:profil,id',
            'niveau_numerique' => 'required|string|in:debutant,intermediaire,avance,expert',
            'experiences' => 'required|array|min:1',
            'competences_techniques' => 'required|array|min:1',
        ], [
            'prenom.required' => 'Votre prénom est requis.',
            'nom.required' => 'Votre nom est requis.',
            'telephone.required' => 'Votre numéro de téléphone est requis.',
            'email.required' => 'Votre adresse email est requise.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'ministere_id.required' => 'Veuillez sélectionner votre structure de provenance.',
            'direction.required' => 'Veuillez préciser votre direction ou service.',
            'profil_id.required' => 'Veuillez choisir le profil souhaité.',
            'niveau_numerique.required' => 'Veuillez indiquer votre niveau numérique.',
            'experiences.required' => 'Veuillez sélectionner au moins une expérience.',
            'competences_techniques.required' => 'Veuillez sélectionner au moins une compétence technique.',
        ]);

        $selectedProfile = Profil::whereKey($validated['profil_id'])->first();
        if (!$selectedProfile || $selectedProfile->code === 'chauffeur') {
            return back()->withErrors([
                'profil_id' => 'Ce profil n\'est pas disponible depuis le formulaire public.',
            ])->withInput();
        }

        $hasNoMatricule = $request->boolean('no_matricule');
        
        if ($hasNoMatricule) {
            $request->validate([
                'cin' => ['required', 'string', 'regex:/^\d{13,14}$/'],
            ], [
                'cin.required' => 'Le numéro de CNI est requis si vous n\'avez pas de matricule.',
                'cin.regex' => 'Le numéro de CNI doit comporter 13 ou 14 chiffres.',
            ]);
            $identityNumber = $request->input('cin');
        } else {
            if (!$request->filled('matricule')) {
                return back()->withErrors(['matricule' => 'Le matricule est requis si vous en possédez un.'])->withInput();
            }
            $identityNumber = $request->input('matricule');
        }

        // Vérification d'existence (par matricule ou téléphone)
        $existing = User::where(function($q) use ($identityNumber, $validated) {
            if (!empty($identityNumber)) $q->where('matricule', $identityNumber);
            $q->orWhere('telephone', $validated['telephone']);
        })->first();

        if ($existing) {
            // Si l'agent est déjà "officiel_inscrit" ou "reserve", il a déjà fait son boulot
            if (in_array($existing->validation_status, ['officiel_inscrit', 'reserve'])) {
                return back()->withErrors([
                    'matricule' => 'Un agent avec ce matricule ou ce numéro de téléphone est déjà enregistré et a finalisé son profil.',
                ])->withInput();
            }
            // Sinon (officiel_attente ou pas de statut), on autorise la réconciliation
            $existingUserId = $existing->id;
        } else {
            $existingUserId = null;
        }

        // Stockage en session pour le wizard multi-étapes
        session([
            'pending_user_payload' => [
                'id' => $existingUserId, // On garde l'ID s'il existe pour la fusion
                'prenom' => $validated['prenom'],
                'nom' => $validated['nom'],
                'matricule' => $identityNumber,
                'telephone' => $validated['telephone'],
                'email' => $validated['email'],
                'ministere_id' => (int) $validated['ministere_id'],
                'direction' => trim($validated['direction']),

                'profil_id' => (int) $validated['profil_id'],
                'niveau_numerique' => $validated['niveau_numerique'],
                'experiences' => array_values(array_unique($validated['experiences'] ?? [])),
                'competences_techniques' => array_values(array_unique($validated['competences_techniques'] ?? [])),
            ],
            'pending_dynamic_answers' => [],
        ]);

        return redirect()->route('user_region_choice.decision');
    }
}
