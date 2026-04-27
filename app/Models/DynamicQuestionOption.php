<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicQuestionOption extends Model
{
    protected $fillable = [
        'dynamic_question_id',
        'libelle',
        'ordre',
    ];

    public function question()
    {
        return $this->belongsTo(DynamicQuestion::class, 'dynamic_question_id');
    }
}
