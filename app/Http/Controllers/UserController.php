<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DynamicQuestion;
use App\Models\Ministere;
use App\Models\User;

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

        $ministeres = Ministere::orderBy('nom')->get();

        return view('utilisateur_form', compact('dynamicQuestions', 'ministeres'));
    }

    /**
        * Valide les informations utilisateur et les conserve temporairement en session.
        * L'inscription en base est finalisée uniquement après le formulaire de choix des régions.
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
            'ministere_id' => 'required|integer|exists:ministeres,id',
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

        // Si matricule fourni, vérifier unicité stricte avant de poursuivre
        if (!empty($validated['matricule'])) {
            $existing = User::where('matricule', $validated['matricule'])->first();
            if ($existing) {
                return back()->withErrors(['matricule' => 'Ce matricule a déjà été utilisé. Vous avez déjà choisi.'])->withInput();
            }
        }

        $dynamicAnswersPayload = [];
        foreach ($dynamicQuestions as $question) {
            $field = 'question_' . $question->id;
            $value = $request->input($field);

            if ($question->type === 'text') {
                if (filled($value)) {
                    $dynamicAnswersPayload[] = [
                        'dynamic_question_id' => $question->id,
                        'answer_text' => trim((string) $value),
                    ];
                }
            }

            if ($question->type === 'select' && filled($value)) {
                $dynamicAnswersPayload[] = [
                    'dynamic_question_id' => $question->id,
                    'dynamic_question_option_id' => (int) $value,
                ];
            }
        }

        session([
            'pending_user_payload' => [
                'prenom' => $validated['prenom'],
                'nom' => $validated['nom'],
                'ministere_id' => (int) $validated['ministere_id'],
                'matricule' => $validated['matricule'] ?? null,
                'telephone' => $validated['telephone'] ?? null,
            ],
            'pending_dynamic_answers' => $dynamicAnswersPayload,
        ]);

        // Rediriger vers la suite (ex: choix des régions)
        return redirect()->route('user_region_choice.create');
    }
}
