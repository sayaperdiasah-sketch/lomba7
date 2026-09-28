<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryItem extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'type',
        'target',
        'unit',
        'description',
        'order',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
