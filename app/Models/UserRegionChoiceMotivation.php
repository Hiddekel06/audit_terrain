<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRegionChoiceMotivation extends Model
{
    protected $table = 'user_region_choice_motivation';
    protected $fillable = ['user_region_choice_id', 'motivation_id', 'motivation_libre'];
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
