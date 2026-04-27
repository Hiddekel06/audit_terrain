<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDynamicAnswer extends Model
{
    protected $fillable = [
        'user_id',
        'dynamic_question_id',
        'dynamic_question_option_id',
        'answer_text',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(DynamicQuestion::class, 'dynamic_question_id');
    }

    public function option()
    {
        return $this->belongsTo(DynamicQuestionOption::class, 'dynamic_question_option_id');
    }
}
