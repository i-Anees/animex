<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name', 'email', 'city', 'status', 'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getOrdersCountAttribute(): int
    {
        return $this->orders()->count();
    }

    public function getSpentAttribute(): float
    {
        return (float) $this->orders()->where('payment_status', 'paid')->sum('total');
    }
}
