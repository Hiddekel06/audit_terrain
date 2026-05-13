<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRegionChoice;
use App\Models\Region;
use App\Models\Motivation;
use App\Models\DynamicQuestion;
use App\Models\UserDynamicAnswer;
use App\Models\UserRegionChoiceMotivation;
use App\Models\Profil;
use App\Models\Ministere;

class AdminDashboardController extends Controller
{
    /**
     * Affiche le dashboard admin avec les statistiques principales.
     */
    public function index()
    {
        $completedUsers = UserRegionChoice::distinct('user_id')->count('user_id');
        // KPI principale: tous les enregistrements dans la table users
        $totalUsers = User::count();

        // IDs des utilisateurs complétés (ayant au moins 1 choix de région)
        $completedUserIds = UserRegionChoice::distinct('user_id')->pluck('user_id')->toArray();

        $totalDynamicAnswers = UserDynamicAnswer::whereIn('user_id', $completedUserIds)->count();
        $dynamicRespondents = UserDynamicAnswer::distinct('user_id')->whereIn('user_id', $completedUserIds)->count('user_id');
        $dynamicRespondentsRate = $completedUsers > 0
            ? (int) round(($dynamicRespondents / $completedUsers) * 100)
            : 0;

        // Distribution par profil sur tous les utilisateurs inscrits
        $candidatesByProfil = User::selectRaw('profil_id, profil.libelle, COUNT(*) as total')
            ->leftJoin('profil', 'users.profil_id', '=', 'profil.id')
            ->groupBy('profil_id', 'profil.libelle')
            ->orderByDesc('total')
            ->get();

        // Distribution par expérience (remplace le niveau numérique)
        $candidatesByExperience = collect();
        $experienceMapping = [
            'audit_recensement' => 'Audit / recensement',
            'biometrie' => 'Biométrie',
            'projets_it' => 'Projets IT',
            'aucune' => 'Aucune'
        ];
        
        // Récupérer tous les utilisateurs avec leurs expériences
        $usersWithExperiences = User::query()
            ->whereNotNull('experiences')
            ->get();
        
        // Compter chaque type d'expérience
        foreach ($experienceMapping as $value => $label) {
            $count = $usersWithExperiences->filter(function($user) use ($value) {
                return is_array($user->experiences) && in_array($value, $user->experiences);
            })->count();
            if ($count > 0) {
                $candidatesByExperience->put($label, $count);
            }
        }

        // Distribution par niveau numérique sur tous les utilisateurs
        $candidatesByNiveau = User::selectRaw('niveau_numerique, COUNT(*) as total')
            ->whereNotNull('niveau_numerique')
            ->groupBy('niveau_numerique')
            ->get()
            ->mapWithKeys(fn($item) => [$item->niveau_numerique => $item->total]);

        // Distribution par disponibilité sur tous les utilisateurs
        $candidatesByDisponibilite = User::selectRaw('disponibilite, COUNT(*) as total')
            ->whereNotNull('disponibilite')
            ->groupBy('disponibilite')
            ->get()
            ->mapWithKeys(fn($item) => [$item->disponibilite => $item->total]);

        // Top ministères sur tous les utilisateurs
        $candidatesByMinistere = User::selectRaw('ministere_id, ministeres.nom, COUNT(*) as total')
            ->leftJoin('ministeres', 'users.ministere_id', '=', 'ministeres.id')
            ->groupBy('ministere_id', 'ministeres.nom')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

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
        $motivations = Motivation::withTrashed()->orderBy('libelle')->get();
        $dynamicQuestions = DynamicQuestion::with('options')
            ->withCount('answers')
            ->orderBy('ordre')
            ->orderBy('id')
            ->get();

        $profils = Profil::where('is_active', true)->get();

        // Comptes pour le déploiement global (Oui / Non)
        $readyYes = User::where('ready_to_deploy_all_regions', true)->count();
        $readyNo = User::where('ready_to_deploy_all_regions', false)->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'completedUsers',
            'totalDynamicAnswers',
            'dynamicRespondents',
            'dynamicRespondentsRate',
            'candidatesByProfil',
            'candidatesByExperience',
            'candidatesByNiveau',
            'candidatesByDisponibilite',
            'candidatesByMinistere',
            'tendances',
            'tendancesChoix1',
            'tendancesChoix2',
            'tendancesChoix3',
            'motivationStats',
            'motivationsLibres',
            'motivations',
            'dynamicQuestions',
            'profils'
            , 'readyYes', 'readyNo'
        ));
    }
}
