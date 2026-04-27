<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DynamicQuestion;
use App\Models\User;
use App\Models\UserDynamicAnswer;

class UserController extends Controller
{
    /**
     * Affiche le formulaire utilisateur avec les questions dynamiques actives.
     */
    public function create()
    {
        $dynamicQuestions = DynamicQuestion::where('is_active', true)
            ->with('options')
            ->orderBy('ordre')
            ->orderBy('id')
            ->get();

        return view('utilisateur_form', compact('dynamicQuestions'));
    }

    /**
     * Enregistre ou identifie un utilisateur selon les règles métier.
     */
    public function store(Request $request)
    {
        $dynamicQuestions = DynamicQuestion::where('is_active', true)
            ->with('options')
            ->orderBy('ordre')
            ->orderBy('id')
            ->get();

        $rules = [
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'matricule' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^\\d{6}[A-Za-z]$/', // 6 chiffres + 1 lettre
            ],
            'telephone' => ['nullable', 'string', 'regex:/^\\d{9}$/'],
        ];

        foreach ($dynamicQuestions as $question) {
            $field = 'question_' . $question->id;

            if ($question->type === 'select') {
                $optionIds = $question->options->pluck('id')->all();
                if (count($optionIds) === 0) {
                    $rules[$field] = 'nullable';
                } else {
                    $rules[$field] = [
                        $question->is_required ? 'required' : 'nullable',
                        'integer',
                        'in:' . implode(',', $optionIds),
                    ];
                }
            } else {
                $rules[$field] = ($question->is_required ? 'required' : 'nullable') . '|string|max:1000';
            }
        }

        $validated = $request->validate($rules, [
            'matricule.regex' => 'Le matricule doit être composé de 6 chiffres suivis d\'une lettre (ex : 000000A).',
            'telephone.regex' => 'Le numéro de téléphone doit contenir exactement 9 chiffres.',
        ]);

        // Vérifier qu'au moins matricule ou téléphone est présent
        if (empty($validated['matricule']) && empty($validated['telephone'])) {
            return back()->withErrors(['matricule' => 'Le matricule ou le téléphone est obligatoire.'])->withInput();
        }

        // Si matricule fourni, vérifier unicité stricte
        if (!empty($validated['matricule'])) {
            $existing = User::where('matricule', $validated['matricule'])->first();
            if ($existing) {
                return back()->withErrors(['matricule' => 'Ce matricule a déjà été utilisé. Vous avez déjà choisi.'])->withInput();
            }
            $user = User::create([
                'matricule' => $validated['matricule'],
                'prenom' => $validated['prenom'],
                'nom' => $validated['nom'],
                'telephone' => $validated['telephone'] ?? null,
            ]);
        } else {
            // Générer un matricule fictif unique pour satisfaire la contrainte DB
            $fakeMatricule = 'T' . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT) . chr(mt_rand(65, 90));
            while (User::where('matricule', $fakeMatricule)->exists()) {
                $fakeMatricule = 'T' . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT) . chr(mt_rand(65, 90));
            }
            $user = User::create([
                'telephone' => $validated['telephone'],
                'prenom' => $validated['prenom'],
                'nom' => $validated['nom'],
                'matricule' => $fakeMatricule,
            ]);
        }

        foreach ($dynamicQuestions as $question) {
            $field = 'question_' . $question->id;
            $value = $request->input($field);

            if ($question->type === 'text') {
                if (filled($value)) {
                    UserDynamicAnswer::create([
                        'user_id' => $user->id,
                        'dynamic_question_id' => $question->id,
                        'answer_text' => trim((string) $value),
                    ]);
                }
            }

            if ($question->type === 'select' && filled($value)) {
                UserDynamicAnswer::create([
                    'user_id' => $user->id,
                    'dynamic_question_id' => $question->id,
                    'dynamic_question_option_id' => (int) $value,
                ]);
            }
        }

        // Authentifier l'utilisateur (optionnel selon logique)
        // Auth::login($user);

        // Stocker l'id utilisateur en session pour la suite
        session(['user_id' => $user->id]);
        // Rediriger vers la suite (ex: choix des régions)
        return redirect()->route('user_region_choice.create');
    }
}
