<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Motivation extends Model
{
    use SoftDeletes;

    protected $fillable = ['libelle'];
    /**
     * Une motivation peut appartenir à plusieurs choix utilisateur.
     */
    public function userRegionChoiceMotivations()
    {
        return $this->hasMany(UserRegionChoiceMotivation::class);
    }
}
