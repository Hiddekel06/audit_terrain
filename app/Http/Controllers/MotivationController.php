<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Motivation;

class MotivationController extends Controller
{
    /**
     * Ajoute une nouvelle motivation (admin).
     */
    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:255|unique:motivations,libelle',
        ]);
        Motivation::create([
            'libelle' => $request->libelle,
        ]);
        return redirect()->route('admin.dashboard')->with('success', 'Motivation ajoutée avec succès.');
    }

    /**
     * Supprime une motivation sans l'effacer définitivement.
     */
    public function destroy(Motivation $motivation)
    {
        $motivation->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Motivation supprimée.');
    }

    /**
     * Restaure une motivation supprimée.
     */
    public function restore(int $motivationId)
    {
        $motivation = Motivation::withTrashed()->findOrFail($motivationId);
        $motivation->restore();

        return redirect()->route('admin.dashboard')->with('success', 'Motivation restaurée.');
    }
}
