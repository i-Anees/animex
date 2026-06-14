<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collection extends Model
{
    protected $fillable = [
        'slug', 'name', 'tag', 'accent', 'tone', 'image_id', 'sort_order',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function drops(): HasMany
    {
        return $this->hasMany(Drop::class);
    }
}
