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
            'matricule' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
        ]);

        // Vérifier qu'au moins matricule ou téléphone est présent
        if (empty($validated['matricule']) && empty($validated['telephone'])) {
            return back()->withErrors(['matricule' => 'Le matricule ou le téléphone est obligatoire.']);
        }

        // Recherche ou création de l'utilisateur
        $user = null;
        if (!empty($validated['matricule'])) {
            $user = \App\Models\User::firstOrCreate(
                ['matricule' => $validated['matricule']],
                [
                    'prenom' => $validated['prenom'],
                    'nom' => $validated['nom'],
                    'telephone' => $validated['telephone'] ?? null,
                ]
            );
        } else {
            $user = \App\Models\User::firstOrCreate(
                ['telephone' => $validated['telephone']],
                [
                    'prenom' => $validated['prenom'],
                    'nom' => $validated['nom'],
                ]
            );
        }

        // Authentifier l'utilisateur (optionnel selon logique)
        // Auth::login($user);

        // Rediriger vers la suite (ex: choix des régions)
        return redirect()->route('region.choix', ['user' => $user->id]);
    }
}
