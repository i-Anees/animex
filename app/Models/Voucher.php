<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'description', 'type', 'value', 'min_subtotal',
        'max_redemptions', 'redeemed_count', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_subtotal' => 'decimal:2',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /** Generate a unique, automated voucher code. */
    public static function generateCode(string $prefix = 'AX'): string
    {
        do {
            $code = $prefix . '-' . strtoupper(Str::random(6));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /** Why a voucher can't be applied (null = valid for this subtotal). */
    public function rejectionReason(float $subtotal): ?string
    {
        if (! $this->is_active) {
            return 'This voucher is no longer active.';
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'This voucher has expired.';
        }
        if (! is_null($this->max_redemptions) && $this->redeemed_count >= $this->max_redemptions) {
            return 'This voucher has reached its redemption limit.';
        }
        if ($subtotal < (float) $this->min_subtotal) {
            return 'Order subtotal must be at least AED ' . (int) $this->min_subtotal . '.';
        }

        return null;
    }

    public function discountFor(float $subtotal): float
    {
        if ($this->type === 'fixed') {
            return round(min((float) $this->value, $subtotal), 2);
        }

        return round($subtotal * ((float) $this->value / 100), 2);
    }

    public function label(): string
    {
        return $this->type === 'fixed'
            ? 'AED ' . (int) $this->value . ' off'
            : (int) $this->value . '% off';
    }
}
