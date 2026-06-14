<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'overline', 'title', 'subtitle', 'image_id', 'image_url',
        'tone', 'accent', 'accent2', 'cta_label', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Final image URL — direct override, else build from the Unsplash id. */
    public function getImageUrlResolvedAttribute(): ?string
    {
        if ($this->image_url) {
            return $this->image_url;
        }
        if ($this->image_id) {
            return 'https://images.unsplash.com/photo-' . $this->image_id . '?w=1600&q=75&auto=format&fit=crop';
        }
        return null;
    }
}
