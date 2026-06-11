<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeploymentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'is_draft',
        'data',
        'summary',
        'metadata',
    ];

    protected $casts = [
        'is_draft' => 'boolean',
        'data' => 'array',
        'summary' => 'array',
        'metadata' => 'array',
    ];
}
