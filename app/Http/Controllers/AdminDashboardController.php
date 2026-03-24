<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRegionChoice;
use App\Models\Region;
use App\Models\UserRegionChoiceMotivation;

class AdminDashboardController extends Controller
{
    /**
     * Affiche le dashboard admin avec les statistiques principales.
     */
    public function index()
    {
        $totalUsers = User::count();
        // Tendances globales (tous choix confondus)
        $tendances = UserRegionChoice::selectRaw('region_id, count(*) as total')
            ->groupBy('region_id')
            ->with('region')
            ->orderByDesc('total')
            ->get();
        // Tendances par choix 1, 2, 3
        $tendancesChoix1 = UserRegionChoice::where('ordre', 1)
            ->selectRaw('region_id, count(*) as total')
            ->groupBy('region_id')
            ->with('region')
            ->orderByDesc('total')
            ->get();
        $tendancesChoix2 = UserRegionChoice::where('ordre', 2)
            ->selectRaw('region_id, count(*) as total')
            ->groupBy('region_id')
            ->with('region')
            ->orderByDesc('total')
            ->get();
        $tendancesChoix3 = UserRegionChoice::where('ordre', 3)
            ->selectRaw('region_id, count(*) as total')
            ->groupBy('region_id')
            ->with('region')
            ->orderByDesc('total')
            ->get();
        $motivationStats = \App\Models\UserRegionChoice::statsMotivations();
        $motivationsLibres = UserRegionChoiceMotivation::whereNotNull('motivation_libre')->orderByDesc('id')->get();
        return view('admin.dashboard', compact('totalUsers', 'tendances', 'tendancesChoix1', 'tendancesChoix2', 'tendancesChoix3', 'motivationStats', 'motivationsLibres'));
    }
}
