<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSection extends Model
{
    protected $fillable = [
        'quiz_id',
        'titre',
        'description',
        'ordre',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'section_id')->orderBy('ordre');
    }
}
