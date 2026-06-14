<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Drop extends Model
{
    protected $fillable = [
        'code', 'name', 'collection_id', 'status',
        'sold', 'total', 'revenue', 'live_at', 'ends_at',
    ];

    protected $casts = [
        'revenue' => 'decimal:2',
        'live_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }
}
