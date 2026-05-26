<?php

namespace App\Http\Controllers;

use App\Models\Ministere;
use Illuminate\Http\Request;

class AdminMinistereStatsController extends Controller
{
    /**
     * Affiche la répartition des agents par ministère.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $ministeres = Ministere::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nom', 'like', '%' . $search . '%');
            })
            ->withCount([
                'users as total_agents',
                'users as chefs_count' => function ($query) {
                    $query->where('profil_id', 1);
                },
                'users as auditeurs_count' => function ($query) {
                    $query->where('profil_id', 2);
                },
                'users as supports_count' => function ($query) {
                    $query->where('profil_id', 3);
                },
            ])
            ->orderByDesc('total_agents')
            ->orderBy('nom')
            ->paginate(12)
            ->withQueryString();

        $totals = [
            'agents' => (int) Ministere::query()->withCount('users')->get()->sum('users_count'),
            'chefs' => (int) Ministere::query()->withCount(['users as chefs_count' => function ($query) {
                $query->where('profil_id', 1);
            }])->get()->sum('chefs_count'),
            'auditeurs' => (int) Ministere::query()->withCount(['users as auditeurs_count' => function ($query) {
                $query->where('profil_id', 2);
            }])->get()->sum('auditeurs_count'),
            'supports' => (int) Ministere::query()->withCount(['users as supports_count' => function ($query) {
                $query->where('profil_id', 3);
            }])->get()->sum('supports_count'),
        ];

        return view('admin.ministeres', compact('ministeres', 'search', 'totals'));
    }
}
