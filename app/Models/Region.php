<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    /**
     * Une région peut être choisie par plusieurs utilisateurs.
     */
    public function userRegionChoices()
    {
        return $this->hasMany(UserRegionChoice::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
