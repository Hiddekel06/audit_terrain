<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRegionChoice;
use App\Models\Region;

class UserRegionChoiceController extends Controller
{
    /**
     * Affiche le formulaire de choix des régions pour l'utilisateur.
     */
    public function create()
    {
        $regions = Region::all();
        return view('choix_regions', compact('regions'));
    }

    /**
     * Enregistre les choix de régions de l'utilisateur.
     */
    public function store(Request $request)
    {
        $request->validate([
            'choix_1' => 'required|different:choix_2,choix_3|exists:regions,id',
            'choix_2' => 'required|different:choix_1,choix_3|exists:regions,id',
            'choix_3' => 'required|different:choix_1,choix_2|exists:regions,id',
        ]);

        // Récupérer l'utilisateur depuis la session (par téléphone ou matricule)
        $user = null;
        if (session()->has('user_id')) {
            $user = \App\Models\User::find(session('user_id'));
        } elseif ($request->has('telephone')) {
            $user = \App\Models\User::where('telephone', $request->input('telephone'))->first();
        } elseif ($request->has('matricule')) {
            $user = \App\Models\User::where('matricule', $request->input('matricule'))->first();
        }
        if (!$user) {
            return redirect()->route('utilisateur.form')->withErrors(['user' => 'Utilisateur introuvable. Veuillez vous identifier.']);
        }
        // On supprime les anciens choix si besoin
        $user->regionChoices()->delete();

        // On enregistre les 3 choix
        foreach ([1,2,3] as $i) {
            UserRegionChoice::create([
                'user_id' => $user->id,
                'region_id' => $request->input('choix_'.$i),
                'ordre' => $i,
            ]);
        }

        return view('merci');
    }
}
