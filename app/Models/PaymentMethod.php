<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'type',
        'label',
        'account_name',
        'account_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'bank'   => 'تحويل بنكي',
            'palpay' => 'PalPay',
            'wallet' => 'محفظة جوال',
            default  => $this->type,
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'bank'   => 'fa-building-columns',
            'palpay' => 'fa-wallet',
            'wallet' => 'fa-mobile-screen',
            default  => 'fa-money-bill',
        };
    }
}
