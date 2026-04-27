<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ministere extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'code',
        'parent_id',
        'region_id',
        'departement_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
