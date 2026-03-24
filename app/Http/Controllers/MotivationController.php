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
}
