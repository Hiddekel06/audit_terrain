<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'region_id'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function members()
    {
        return $this->hasMany(User::class);
    }
}
