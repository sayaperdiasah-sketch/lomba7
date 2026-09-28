<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleConfirmation extends Model
{
    protected $table = 'schedule_confirmations';

    protected $fillable = [
        'user_id',
        'scheduled_date',
        'done_count',
        'confirmed_at',
        'items',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'confirmed_at' => 'datetime',
        'items' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
