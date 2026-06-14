<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\HeroSlide;
use App\Models\Product;

class StorefrontController extends Controller
{
    /** Tonal fallback pairs — mirrors the prototype's TONES array. */
    private const TONES = [
        ['#F5F5F5', '#2D2D2D'], ['#EDEDED', '#000000'], ['#F0F0F0', '#1f1f1f'],
        ['#2D2D2D', '#F5F5F5'], ['#000000', '#2D2D2D'], ['#E8E8E8', '#111111'],
    ];

    public function home()
    {
        $collections = Collection::orderBy('sort_order')->get()->map(fn ($c) => [
            'id' => $c->slug,
            'name' => $c->name,
            'tag' => $c->tag,
            'tone' => $c->tone,
            'img' => $c->image_id,
            'accent' => $c->accent,
        ])->values();

        $categories = Category::orderBy('sort_order')->pluck('name')->values();

        $hero = HeroSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($h) => [
                'over' => $h->overline,
                'title' => $h->title,
                'sub' => $h->subtitle,
                'tone' => $h->tone,
                'accent' => $h->accent,
                'accent2' => $h->accent2,
                'cta' => $h->cta_label,
                'imgUrl' => $h->image_url_resolved,
            ])->values();

        $products = Product::with('collection', 'category')
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->values()
            ->map(fn ($p, $i) => [
                'id' => $p->slug,
                '_id' => $p->id,
                'sku' => $p->sku,
                'title' => $p->title,
                'category' => $p->category?->name,
                'collection' => $p->collection?->slug,
                'collectionName' => $p->collection?->name,
                'drop' => $p->drop_label,
                'price' => (int) round($p->price),
                'sale' => $p->sale_price !== null ? (int) round($p->sale_price) : null,
                'tones' => self::TONES[$i % count(self::TONES)],
                'img' => asset($p->image),
                'imgHover' => asset($p->image_hover),
                'gallery' => collect($p->gallery ?? [])->map(fn ($g) => asset($g))->all(),
                'sizes' => $p->sizes ?? [],
                'soldOutSizes' => $p->sold_out_sizes ?? [],
                'colors' => $p->colors ?? [],
                'edition' => $p->edition,
                'rating' => (float) $p->rating,
                'reviews' => $p->reviews_count,
                'isNew' => (bool) $p->is_new,
                'isBest' => (bool) $p->is_best,
                'isLimited' => (bool) $p->is_limited,
                'stock' => $p->stock,
                'blurbShort' => $p->blurb_short,
                'desc' => $p->description,
            ]);

        return view('store.app', [
            'data' => [
                'collections' => $collections,
                'categories' => $categories,
                'products' => $products,
                'hero' => $hero,
            ],
        ]);
    }
}
