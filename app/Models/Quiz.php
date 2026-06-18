<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'titre',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id')->orderBy('ordre');
    }

    public function sections()
    {
        return $this->hasMany(QuizSection::class)->orderBy('ordre');
    }

    public function results()
    {
        return $this->hasMany(QuizResult::class);
    }

    public function profils()
    {
        return $this->belongsToMany(Profil::class, 'quiz_profil');
    }
}
