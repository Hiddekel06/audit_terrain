<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminApiTokenController extends Controller
{
    /**
     * Affiche la liste des tokens API.
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $tokens = $admin->tokens()->orderBy('created_at', 'desc')->get();
        
        return view('admin.api-tokens', compact('tokens'));
    }

    /**
     * Génère un nouveau token pour une plateforme tierce.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $admin = Auth::guard('admin')->user();
        $token = $admin->createToken($request->name);

        return back()->with('success', 'Token créé avec succès. Copiez-le maintenant, il ne sera plus affiché ensuite.')
                     ->with('plainTextToken', $token->plainTextToken);
    }

    /**
     * Révoque un token existant.
     */
    public function destroy($id)
    {
        $admin = Auth::guard('admin')->user();
        $admin->tokens()->where('id', $id)->delete();

        return back()->with('success', 'L\'accès pour ce token a été révoqué.');
    }
}
