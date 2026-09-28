<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    protected $fillable = [
        'name',
        'spesialisasi',
        'jam_praktik',
        'bio',
        'whatsapp',
        'website',
        'avatar',
        'photo',
        'online',
    ];

    protected $casts = [
        'online' => 'boolean',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(ConsultationBooking::class);
    }
}
