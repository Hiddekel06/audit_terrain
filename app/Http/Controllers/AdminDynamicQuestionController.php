<?php

namespace App\Http\Controllers;

use App\Models\DynamicQuestion;
use App\Models\DynamicQuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDynamicQuestionController extends Controller
{
    /**
     * Ajoute une question dynamique simple (text/select).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'type' => 'required|in:text,select',
            'is_required' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'ordre' => 'nullable|integer|min:0',
            'placeholder' => 'nullable|string|max:255',
            'options_text' => 'nullable|string|required_if:type,select',
        ]);

        $question = DynamicQuestion::create([
            'libelle' => $validated['libelle'],
            'type' => $validated['type'],
            'is_required' => (bool) ($request->boolean('is_required')),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
            'ordre' => (int) ($validated['ordre'] ?? 0),
            'placeholder' => $validated['placeholder'] ?? null,
        ]);

        if ($question->type === 'select' && !empty($validated['options_text'])) {
            $options = collect(explode(',', $validated['options_text']))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values();

            if ($options->isEmpty()) {
                $question->delete();

                return redirect()
                    ->route('admin.dashboard')
                    ->withErrors(['options_text' => 'Ajoute au moins une option valide pour une question de type liste.'])
                    ->withInput();
            }

            foreach ($options as $index => $optionLabel) {
                DynamicQuestionOption::create([
                    'dynamic_question_id' => $question->id,
                    'libelle' => $optionLabel,
                    'ordre' => $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.dashboard')->with('success', 'Question dynamique ajoutée.');
    }

    /**
     * Met à jour une question dynamique existante.
     */
    public function update(Request $request, DynamicQuestion $question)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'type' => 'required|in:text,select',
            'is_required' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'ordre' => 'nullable|integer|min:0',
            'placeholder' => 'nullable|string|max:255',
            'options_text' => 'nullable|string|required_if:type,select',
        ]);

        $options = collect();
        if ($validated['type'] === 'select') {
            $options = collect(explode(',', (string) ($validated['options_text'] ?? '')))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values();

            if ($options->isEmpty()) {
                return redirect()
                    ->route('admin.dashboard')
                    ->withErrors(['options_text' => 'Ajoute au moins une option valide pour une question de type liste.'])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($request, $question, $validated, $options) {
            $question->update([
                'libelle' => $validated['libelle'],
                'type' => $validated['type'],
                'is_required' => (bool) ($request->boolean('is_required')),
                'is_active' => $request->has('is_active') ? $request->boolean('is_active') : false,
                'ordre' => (int) ($validated['ordre'] ?? 0),
                'placeholder' => $validated['placeholder'] ?? null,
            ]);

            if ($question->type === 'text') {
                $question->options()->delete();
                return;
            }

            // Synchronisation simple pour l'étape 1: on remplace la liste entière.
            $question->options()->delete();

            foreach ($options as $index => $optionLabel) {
                DynamicQuestionOption::create([
                    'dynamic_question_id' => $question->id,
                    'libelle' => $optionLabel,
                    'ordre' => $index + 1,
                ]);
            }
        });

        return redirect()->route('admin.dashboard')->with('success', 'Question dynamique modifiée.');
    }

    /**
     * Active ou désactive une question.
     */
    public function toggle(DynamicQuestion $question)
    {
        $question->is_active = !$question->is_active;
        $question->save();

        return redirect()->route('admin.dashboard')->with('success', 'Statut de la question mis à jour.');
    }

    /**
     * Met à jour l'ordre d'affichage de la question.
     */
    public function updateOrder(Request $request, DynamicQuestion $question)
    {
        $validated = $request->validate([
            'ordre' => 'required|integer|min:0',
        ]);

        $question->update([
            'ordre' => (int) $validated['ordre'],
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Ordre de la question mis à jour.');
    }
}
