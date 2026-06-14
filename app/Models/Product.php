<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'sku', 'slug', 'title', 'collection_id', 'category_id', 'drop_label',
        'price', 'sale_price', 'edition', 'rating', 'reviews_count',
        'is_new', 'is_best', 'is_limited', 'is_active', 'stock',
        'sizes', 'sold_out_sizes', 'colors', 'gallery',
        'image', 'image_hover', 'blurb_short', 'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'rating' => 'decimal:1',
        'is_new' => 'boolean',
        'is_best' => 'boolean',
        'is_limited' => 'boolean',
        'is_active' => 'boolean',
        'sizes' => 'array',
        'sold_out_sizes' => 'array',
        'colors' => 'array',
        'gallery' => 'array',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getCurrentPriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    public function getOnSaleAttribute(): bool
    {
        return ! is_null($this->sale_price);
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }
}
