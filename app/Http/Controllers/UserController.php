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
    public function create()
    {
        $profils = Profil::where('is_active', true)
            ->where('code', '!=', 'chauffeur')
            ->orderBy('ordre')
            ->orderBy('id')
            ->get();

        $ministeres = Ministere::orderBy('nom')->get();

        $selectedProfileId = (int) request()->query('profil_id', old('profil_id'));
        $selectedProfile = $profils->firstWhere('id', $selectedProfileId) ?? $profils->first();

        return view('utilisateur_form', compact('profils', 'selectedProfile', 'ministeres'));
    }

    /**
        * Valide les informations utilisateur et les conserve temporairement en session.
        * L'inscription en base est finalisée uniquement après le formulaire de choix des régions.
     */
    public function store(Request $request)
    {
        $rules = [
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'telephone' => ['required', 'digits:9'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'ministere_id' => ['required', 'integer', 'exists:ministeres,id'],
            'direction' => ['required', 'string', 'max:255'],

            'no_matricule' => ['nullable', 'boolean'],
            'matricule' => ['required_without:no_matricule', 'nullable', 'regex:/^\d{6}[A-Za-z]$/'],
            'cin' => ['required_if:no_matricule,1', 'nullable', 'digits:13'],
            'profil_id' => ['required', 'integer', 'exists:profil,id'],
            'niveau_numerique' => ['required', 'in:debutant,intermediaire,avance,expert'],
            'experiences' => ['required', 'array', 'min:1'],
            'experiences.*' => ['in:audit_recensement,biometrie,projets_it,aucune'],
            'competences_techniques' => ['required', 'array', 'min:1'],
            'competences_techniques.*' => ['in:tablette_smartphone,kit_biometrique,reseau_4g_hotspot,support_technique,excel_donnees,aucune'],
        ];
        $validated = $request->validate($rules, [
            'telephone.digits' => 'Le numéro de téléphone doit contenir exactement 9 chiffres.',            'ministere_id.required' => 'Veuillez sélectionner un ministère.',
            'ministere_id.exists' => 'Le ministère sélectionné est invalide.',            'matricule.required_without' => 'Le matricule est obligatoire si vous ne cochez pas la case "Pas de matricule".',
            'matricule.regex' => 'Le matricule doit contenir 6 chiffres suivis d’une lettre.',
            'direction.required' => 'Veuillez renseigner votre direction.',
            'cin.digits' => 'Le CIN doit contenir exactement 13 chiffres.',
            'cin.required_if' => 'Le CIN est obligatoire lorsque vous cochez "Pas de matricule".',
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
        $identityNumber = $hasNoMatricule
            ? $validated['cin']
            : strtoupper(trim($validated['matricule']));

        if (User::where('matricule', $identityNumber)->exists()) {
            return back()->withErrors([
                $hasNoMatricule ? 'cin' : 'matricule' => $hasNoMatricule
                    ? 'Ce CIN a déjà été utilisé. Vous avez déjà choisi.'
                    : 'Ce matricule a déjà été utilisé. Vous avez déjà choisi.',
            ])->withInput();
        }

        session([
            'pending_user_payload' => [
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
