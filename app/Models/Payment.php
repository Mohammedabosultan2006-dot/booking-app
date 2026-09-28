<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'customer_id',
        'provider_id',
        'payment_method_id',
        'amount',
        'method',
        'status',
        'reference_number',
        'proof_path',
        'submitted_at',
        'verified_at',
        'rejected_reason',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'submitted_at' => 'datetime',
        'verified_at'  => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isSubmitted(): bool { return $this->status === 'submitted'; }
    public function isVerified(): bool  { return $this->status === 'verified'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }

    public function getProofUrlAttribute(): ?string
    {
        return $this->proof_path ? asset('storage/' . $this->proof_path) : null;
    }
}
