<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\AgentResource;
use Illuminate\Http\Request;

class AgentApiController extends Controller
{
    /**
     * Liste des agents avec filtres optionnels.
     */
    public function index(Request $request)
    {
        $query = User::with(['profil', 'ministere', 'team' , 'quizResults']);

        // Filtre par statut (ex: ?status=officiel_inscrit)
        if ($request->filled('status')) {
            $query->where('validation_status', $request->status);
        }

        // Filtre par profil (ex: ?profil=auditeur)
        if ($request->filled('profil')) {
            $query->whereHas('profil', function($q) use ($request) {
                $q->where('code', $request->profil);
            });
        }

        return AgentResource::collection($query->paginate(50));
    }
}
