<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class AdminRegionPriorityController extends Controller
{
    /**
     * Affiche la vue synthèse des régions avec le volume de choix par priorité.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $regions = Region::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nom', 'like', '%' . $search . '%');
            })
            ->withCount([
                'userRegionChoices as total_choices',
                'userRegionChoices as ordre_1_count' => function ($query) {
                    $query->where('ordre', 1);
                },
                'userRegionChoices as ordre_2_count' => function ($query) {
                    $query->where('ordre', 2);
                },
                'userRegionChoices as ordre_3_count' => function ($query) {
                    $query->where('ordre', 3);
                },
            ])
            ->orderByDesc('total_choices')
            ->orderBy('nom')
            ->paginate(10)
            ->withQueryString();

        return view('admin/regions-priority', compact('regions', 'search'));
    }

    /**
     * Affiche le détail des utilisateurs ayant choisi une région.
     */
    public function show(Request $request, Region $region)
    {
        $orderFilter = $request->query('ordre');
        $search = trim((string) $request->query('q', ''));

        $choices = $region->userRegionChoices()
            ->with('user')
            ->when(in_array((int) $orderFilter, [1, 2, 3], true), function ($query) use ($orderFilter) {
                $query->where('ordre', (int) $orderFilter);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery
                        ->where('nom', 'like', '%' . $search . '%')
                        ->orWhere('prenom', 'like', '%' . $search . '%')
                        ->orWhere('matricule', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('ordre')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin/region-priority-detail', [
            'region' => $region,
            'choices' => $choices,
            'orderFilter' => $orderFilter,
            'search' => $search,
        ]);
    }
}
