<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = [
        'quiz_id',
        'section_id',
        'libelle',
        'type',
        'points',
        'ordre',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function section()
    {
        return $this->belongsTo(QuizSection::class, 'section_id');
    }

    public function options()
    {
        return $this->hasMany(QuizOption::class, 'quiz_question_id');
    }
}
