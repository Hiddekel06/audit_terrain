<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRegionChoiceMotivation extends Model
{
    /**
     * Ce lien appartient à un choix utilisateur.
     */
    public function userRegionChoice()
    {
        return $this->belongsTo(UserRegionChoice::class);
    }

    /**
     * Ce lien concerne une motivation.
     */
    public function motivation()
    {
        return $this->belongsTo(Motivation::class);
    }
}
