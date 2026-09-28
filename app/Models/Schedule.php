<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable = [
        'user_id',
        'scheduled_date',
        'title',
        'notes',
        'is_confirmed',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'is_confirmed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ScheduleItem::class);
    }
}
