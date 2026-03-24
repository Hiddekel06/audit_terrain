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
}
