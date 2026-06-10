<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeploymentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'data',
        'summary',
        'metadata',
    ];

    protected $casts = [
        'data' => 'array',
        'summary' => 'array',
        'metadata' => 'array',
    ];
}
