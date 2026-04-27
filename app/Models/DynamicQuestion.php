<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicQuestion extends Model
{
    protected $fillable = [
        'libelle',
        'type',
        'is_required',
        'is_active',
        'ordre',
        'placeholder',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function options()
    {
        return $this->hasMany(DynamicQuestionOption::class)->orderBy('ordre');
    }

    public function answers()
    {
        return $this->hasMany(UserDynamicAnswer::class);
    }
}
