<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MentalTest extends Model
{
    protected $fillable = [
        'jenis',
        'judul',
        'gejala_singkat',
        'warna',
        'detail',
        'deskripsi_tes',
        'intro',
        'soal',
        'options',
        'diagnosis',
        'impacts',
        'recommendations',
        'when_to_seek_help',
        'is_active',
    ];

    protected $casts = [
        'soal' => 'array',
        'options' => 'array',
        'diagnosis' => 'array',
        'impacts' => 'array',
        'recommendations' => 'array',
        'when_to_seek_help' => 'array',
        'is_active' => 'boolean',
    ];

    public function results(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }
}
