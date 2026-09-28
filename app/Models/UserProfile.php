<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nama',
        'usia',
        'gender',
        'goldar',
        'tinggi',
        'berat',
        'avatar',
        'bio',
        'profile_completed_at',
    ];

    protected $casts = [
        'profile_completed_at' => 'datetime',
        'tinggi' => 'integer',
        'berat' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
