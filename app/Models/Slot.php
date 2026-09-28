<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'start_at',
        'end_at',
        'is_booked',
    ];

    protected $casts = [
        'start_at'  => 'datetime',
        'end_at'    => 'datetime',
        'is_booked' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────
    public function scopeAvailable($query)
    {
        return $query->where('is_booked', false)->where('start_at', '>', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_at', '>', now())->orderBy('start_at');
    }

    // ─── Helpers ──────────────────────────────────────────────────
    public function getDurationMinutesAttribute(): int
    {
        return $this->start_at->diffInMinutes($this->end_at);
    }
}
