<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'name',
        'description',
        'duration_minutes',
        'price',
        'location',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'price'            => 'decimal:2',
        'duration_minutes' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────
    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Helpers ──────────────────────────────────────────────────
    public function getAvailableSlotsCount(): int
    {
        return $this->slots()->where('is_booked', false)->where('start_at', '>', now())->count();
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /** لون متدرّج ثابت لكل خدمة، يُستخدم كخلفية بديلة عند غياب الصورة */
    public function getAccentAttribute(): array
    {
        $palettes = [
            ['from' => 'from-indigo-500',  'to' => 'to-purple-600'],
            ['from' => 'from-amber-500',   'to' => 'to-orange-600'],
            ['from' => 'from-emerald-500', 'to' => 'to-teal-600'],
            ['from' => 'from-rose-500',    'to' => 'to-pink-600'],
            ['from' => 'from-sky-500',     'to' => 'to-blue-600'],
        ];

        $palette = $palettes[$this->id % count($palettes)];
        $palette['icon'] = str_contains($this->name, 'أسنان') ? 'fa-tooth' : 'fa-suitcase-medical';

        return $palette;
    }
}
