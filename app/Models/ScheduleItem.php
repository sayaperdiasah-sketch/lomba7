<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleItem extends Model
{
    protected $fillable = [
        'schedule_id',
        'category_id',
        'category_item_id',
        'name',
        'type',
        'target',
        'unit',
        'description',
        'done',
    ];

    protected $casts = [
        'done' => 'boolean',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categoryItem(): BelongsTo
    {
        return $this->belongsTo(CategoryItem::class);
    }
}
