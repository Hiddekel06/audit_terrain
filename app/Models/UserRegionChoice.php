<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRegionChoice extends Model
{
    protected $fillable = ['user_id', 'region_id', 'ordre'];
    /**
     * Ce choix appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ce choix concerne une région.
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Ce choix a plusieurs motivations (pivot).
     */
    public function userRegionChoiceMotivations()
    {
        return $this->hasMany(UserRegionChoiceMotivation::class);
    }

    /**
     * Accès direct aux motivations via la table pivot.
     */
    public function motivations()
    {
        return $this->belongsToMany(Motivation::class, 'user_region_choice_motivation');
    }

    /**
     * Motivations personnalisées (texte libre)
     */
    public function motivationsLibres()
    {
        return $this->hasMany(UserRegionChoiceMotivation::class)
            ->whereNotNull('motivation_libre');
    }

    /**
     * Motivations types (liées à un ID)
     */
    public function motivationsTypes()
    {
        return $this->hasMany(UserRegionChoiceMotivation::class)
            ->whereNotNull('motivation_id');
    }

    /**
     * Statistiques : régions les plus choisies (tous ordres confondus)
     * Retourne une collection [region_id, total, region]
     */
    public static function tendancesGlobales()
    {
        return self::select('region_id', \DB::raw('count(*) as total'))
            ->groupBy('region_id')
            ->orderByDesc('total')
            ->with('region')
            ->get();
    }

    /**
     * Statistiques : régions les plus choisies pour un ordre donné (1, 2 ou 3)
     * Retourne une collection [region_id, total, region]
     */
    public static function tendancesParOrdre($ordre)
    {
        return self::select('region_id', \DB::raw('count(*) as total'))
            ->where('ordre', $ordre)
            ->groupBy('region_id')
            ->orderByDesc('total')
            ->with('region')
            ->get();
    }

    /**
     * Statistiques globales sur les motivations (types et personnalisées)
     */
    public static function statsMotivations()
    {
        $nbTypes = \App\Models\UserRegionChoiceMotivation::whereNotNull('motivation_id')->count();
        $nbLibres = \App\Models\UserRegionChoiceMotivation::whereNotNull('motivation_libre')->count();
        return [
            'types' => $nbTypes,
            'libres' => $nbLibres,
        ];
    }
}
