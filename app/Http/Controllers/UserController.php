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
        return redirect()->route('user_region_choice.create');
    }
}
