<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationProgram extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'emoji',
        'color',
        'description',
        'activities',
        'is_active',
    ];

    protected $casts = [
        'activities' => 'array',
        'is_active' => 'boolean',
    ];
}