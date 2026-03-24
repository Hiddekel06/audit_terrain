<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Motivation extends Model
{
    protected $fillable = ['libelle'];
    /**
     * Une motivation peut appartenir à plusieurs choix utilisateur.
     */
    public function userRegionChoiceMotivations()
    {
        return $this->hasMany(UserRegionChoiceMotivation::class);
    }
}
