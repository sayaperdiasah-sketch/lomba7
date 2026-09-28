<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    protected $fillable = [
        'user_id',
        'mental_test_id',
        'answers',
        'score',
        'level_label',
        'label',
        'sublabel',
        'detail',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mentalTest(): BelongsTo
    {
        return $this->belongsTo(MentalTest::class);
    }
}
