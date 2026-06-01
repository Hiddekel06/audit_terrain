<?php

namespace App\Http\Controllers;

use App\Models\Ministere;
use App\Models\Profil;
use Illuminate\Http\Request;

class AdminMinistereStatsController extends Controller
{
    /**
     * Affiche la répartition des agents par ministère.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $profileCodes = [
            'chef_equipe' => 'Chefs d\'équipe',
            'auditeur' => 'Auditeurs',
            'auditeur_it' => 'Supports',
            'chauffeur' => 'Chauffeurs',
        ];

        $profileIds = Profil::query()
            ->whereIn('code', array_keys($profileCodes))
            ->pluck('id', 'code');

        $profileCountConstraint = function (string $code) use ($profileIds) {
            return function ($query) use ($profileIds, $code) {
                $profileId = $profileIds->get($code);

                if ($profileId) {
                    $query->where('profil_id', $profileId);
                } else {
                    $query->whereRaw('1 = 0');
                }
            };
        };

        $ministeres = Ministere::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nom', 'like', '%' . $search . '%');
            })
            ->withCount([
                'users as total_agents',
                'users as chefs_count' => $profileCountConstraint('chef_equipe'),
                'users as auditeurs_count' => $profileCountConstraint('auditeur'),
                'users as supports_count' => $profileCountConstraint('auditeur_it'),
                'users as chauffeurs_count' => $profileCountConstraint('chauffeur'),
            ])
            ->orderByDesc('total_agents')
            ->orderBy('nom')
            ->paginate(12)
            ->withQueryString();

        $totals = [
            'agents' => (int) Ministere::query()->withCount('users')->get()->sum('users_count'),
            'chefs' => (int) Ministere::query()->withCount(['users as chefs_count' => $profileCountConstraint('chef_equipe')])->get()->sum('chefs_count'),
            'auditeurs' => (int) Ministere::query()->withCount(['users as auditeurs_count' => $profileCountConstraint('auditeur')])->get()->sum('auditeurs_count'),
            'supports' => (int) Ministere::query()->withCount(['users as supports_count' => $profileCountConstraint('auditeur_it')])->get()->sum('supports_count'),
            'chauffeurs' => (int) Ministere::query()->withCount(['users as chauffeurs_count' => $profileCountConstraint('chauffeur')])->get()->sum('chauffeurs_count'),
        ];

        return view('admin.ministeres', compact('ministeres', 'search', 'totals'));
    }
}
