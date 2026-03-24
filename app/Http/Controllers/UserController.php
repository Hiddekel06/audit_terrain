<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Enregistre ou identifie un utilisateur selon les règles métier.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'matricule' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^\\d{6}[A-Za-z]$/', // 6 chiffres + 1 lettre
            ],
            'telephone' => 'nullable|string|max:255',
        ], [
            'matricule.regex' => 'Le matricule doit être composé de 6 chiffres suivis d\'une lettre (ex : 000000A).',
        ]);

        // Vérifier qu'au moins matricule ou téléphone est présent
        if (empty($validated['matricule']) && empty($validated['telephone'])) {
            return back()->withErrors(['matricule' => 'Le matricule ou le téléphone est obligatoire.'])->withInput();
        }

        // Si matricule fourni, vérifier unicité stricte
        if (!empty($validated['matricule'])) {
            $existing = \App\Models\User::where('matricule', $validated['matricule'])->first();
            if ($existing) {
                return back()->withErrors(['matricule' => 'Ce matricule a déjà été utilisé. Vous avez déjà choisi.'])->withInput();
            }
            $user = \App\Models\User::create([
                'matricule' => $validated['matricule'],
                'prenom' => $validated['prenom'],
                'nom' => $validated['nom'],
                'telephone' => $validated['telephone'] ?? null,
            ]);
        } else {
            // Générer un matricule fictif unique pour satisfaire la contrainte DB
            $fakeMatricule = 'T' . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT) . chr(mt_rand(65, 90));
            while (\App\Models\User::where('matricule', $fakeMatricule)->exists()) {
                $fakeMatricule = 'T' . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT) . chr(mt_rand(65, 90));
            }
            $user = \App\Models\User::create([
                'telephone' => $validated['telephone'],
                'prenom' => $validated['prenom'],
                'nom' => $validated['nom'],
                'matricule' => $fakeMatricule,
            ]);
        }

        // Authentifier l'utilisateur (optionnel selon logique)
        // Auth::login($user);

        // Stocker l'id utilisateur en session pour la suite
        session(['user_id' => $user->id]);
        // Rediriger vers la suite (ex: choix des régions)
        return redirect()->route('user_region_choice.create');
    }
}
