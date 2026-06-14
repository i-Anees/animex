<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        $slides = [
            [
                'overline' => 'Drop 014 — Live Now',
                'title' => 'Ninja Legacy',
                'subtitle' => 'Chapter Three. Heavyweight cotton, numbered to 300. The hidden village, reissued for the street.',
                'image_id' => '1492288991661-058aa541ff43',
                'tone' => '#06121c', 'accent' => '#2BE2FF', 'accent2' => '#1E6BFF',
                'sort_order' => 0,
            ],
            [
                'overline' => 'New Series',
                'title' => 'Saiyan Energy',
                'subtitle' => 'Beyond limits. Garment-dyed heavyweights and technical outerwear engineered to outlast the hype.',
                'image_id' => '1490114538077-0a7f8cb49891',
                'tone' => '#1c1405', 'accent' => '#FFC633', 'accent2' => '#FF7A1A',
                'sort_order' => 1,
            ],
            [
                'overline' => 'Final Sale',
                'title' => 'Dark Curse',
                'subtitle' => 'The cursed archive — last pieces, up to 30% off. When it’s gone, it does not return.',
                'image_id' => '1503341504253-dff4815485f1',
                'tone' => '#14041a', 'accent' => '#C95BFF', 'accent2' => '#FF2E88',
                'sort_order' => 2,
            ],
        ];

        foreach ($slides as $s) {
            HeroSlide::updateOrCreate(['title' => $s['title']], $s);
        }
    }
}
