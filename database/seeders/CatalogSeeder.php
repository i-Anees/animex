<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Drop;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CatalogSeeder extends Seeder
{
    /** Mirror of the JS rng: Math.sin(seed) * 10000, fractional part. */
    private function rng(float $seed): float
    {
        $x = sin($seed) * 10000;
        return $x - floor($x);
    }

    private function photoUrl(string $id, int $w = 800): string
    {
        return "https://images.unsplash.com/photo-{$id}?w={$w}&q=75&auto=format&fit=crop";
    }

    public function run(): void
    {
        $collections = [
            ['slug' => 'demon-slayer',  'name' => 'Demon Slayer',  'tag' => 'Blade Division',  'tone' => '#0c1a1e', 'image_id' => '1503341504253-dff4815485f1', 'accent' => '#2BE2A8'],
            ['slug' => 'attack-titan',  'name' => 'Attack Titan',  'tag' => 'Survey Corps',    'tone' => '#1a1410', 'image_id' => '1490114538077-0a7f8cb49891', 'accent' => '#C8A24B'],
            ['slug' => 'solo-hunter',   'name' => 'Solo Hunter',   'tag' => 'Shadow Monarch',  'tone' => '#0e0a1e', 'image_id' => '1517841905240-472988babdf9', 'accent' => '#7B5BFF'],
            ['slug' => 'ninja-legacy',  'name' => 'Ninja Legacy',  'tag' => 'Hidden Leaf',     'tone' => '#06121c', 'image_id' => '1492288991661-058aa541ff43', 'accent' => '#2BC4FF'],
            ['slug' => 'pirate-king',   'name' => 'Pirate King',   'tag' => 'Grand Line',      'tone' => '#1c1206', 'image_id' => '1556821840-3a63f95609a7',  'accent' => '#FF9D2B'],
            ['slug' => 'dark-curse',    'name' => 'Dark Curse',    'tag' => 'Cursed Energy',   'tone' => '#16061a', 'image_id' => '1618354691373-d851c5c3a990', 'accent' => '#C95BFF'],
            ['slug' => 'ghoul-core',    'name' => 'Ghoul Core',    'tag' => 'Ward Eighteen',   'tone' => '#1a0610', 'image_id' => '1469334031218-e382a71b716b', 'accent' => '#FF2E6B'],
            ['slug' => 'saiyan-energy', 'name' => 'Saiyan Energy', 'tag' => 'Beyond Limits',   'tone' => '#1c1405', 'image_id' => '1521572163474-6864f9cf17ab', 'accent' => '#FFC633'],
        ];
        $collModels = [];
        foreach ($collections as $i => $c) {
            $collModels[$i] = Collection::create($c + ['sort_order' => $i]);
        }

        $categoryNames = ['Hoodies', 'Tees', 'Outerwear', 'Bottoms', 'Headwear', 'Accessories'];
        $catModels = [];
        foreach ($categoryNames as $i => $name) {
            $catModels[$name] = Category::create([
                'slug' => \Illuminate\Support\Str::slug($name),
                'name' => $name,
                'sort_order' => $i,
            ]);
        }

        $photos = [
            '1521572163474-6864f9cf17ab', '1503341504253-dff4815485f1', '1490481651871-ab68de25d43d',
            '1492288991661-058aa541ff43', '1483985988355-763728e1935b', '1551232864-3f0890e580d9',
            '1620799140408-edc6dcb6d633', '1576566588028-4147f3842f27', '1556821840-3a63f95609a7',
            '1618354691373-d851c5c3a990', '1512436991641-6745cdb1723f', '1469334031218-e382a71b716b',
            '1485231183945-fffde7cc051e', '1441986300917-64674bd600d8', '1487222477894-8943e31ef7b2',
            '1499714608240-22fc6ad53fb2', '1490114538077-0a7f8cb49891', '1564584217132-2271feaeb3c5',
            '1517841905240-472988babdf9', '1438761681033-6461ffad8d80',
        ];
        $pool = [0, 1, 7, 14, 8, 9, 6, 17, 3, 16, 18, 5, 2, 10, 11, 4, 12, 13, 15, 19];
        $photo = function (int $i, int $w = 800) use ($photos) {
            $n = count($photos);
            $idx = (($i % $n) + $n) % $n;
            return $this->photoUrl($photos[$idx], $w);
        };

        $names = [
            ['Shadow Clan Hoodie', 'Hoodies'], ['Breathing Form Tee', 'Tees'], ['Survey Corps Bomber', 'Outerwear'],
            ['Monarch Heavyweight', 'Hoodies'], ['Cursed Seal Tee', 'Tees'], ['Grand Line Cargo', 'Bottoms'],
            ['Hidden Leaf Crew', 'Hoodies'], ['Beyond Limits Tee', 'Tees'], ['Ward 18 Trench', 'Outerwear'],
            ['Ki Surge Hoodie', 'Hoodies'], ['Blade Division Cap', 'Headwear'], ['Domain Zip Hoodie', 'Hoodies'],
            ['Wano Box Tee', 'Tees'], ['Eternal Mangekyō Crew', 'Hoodies'], ['Titan Shifter Parka', 'Outerwear'],
            ['Nichirin Tote', 'Accessories'], ['Pirate King Sweatpant', 'Bottoms'], ['Kagune Beanie', 'Headwear'],
            ['Sukuna Oversize Tee', 'Tees'], ['Super Saiyan Track Pant', 'Bottoms'], ['Slayer Mark Cap', 'Headwear'],
            ['Rasengan Knit', 'Hoodies'], ['Jolly Roger Tee', 'Tees'], ['Limitless Vest', 'Outerwear'],
        ];

        foreach ($names as $i => $n) {
            $coll = $collModels[$i % count($collModels)];
            $base = 78 + round($this->rng($i + 1) * 160 / 4) * 4;
            $onSale = $i % 4 === 0;
            $sale = $onSale ? $base - round($base * 0.3 / 4) * 4 : null;
            $r = 3.6 + $this->rng($i + 7) * 1.4;
            $pi = $pool[$i % count($pool)];
            $soldOut = $i % 5 === 0 ? ['XXL'] : ($i % 7 === 0 ? ['S', 'XXL'] : []);
            $stock = $i % 9 === 0 ? 0 : ($i % 6 === 0 ? 4 : 40 + (int) round($this->rng($i) * 120));
            $editionRun = 200 + ($i % 4) * 100;
            $num = str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT);

            Product::create([
                'sku' => 'AX-' . strtoupper(substr($coll->slug, 0, 2)) . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
                'slug' => 'ax-' . $num,
                'title' => $n[0],
                'collection_id' => $coll->id,
                'category_id' => $catModels[$n[1]]->id,
                'drop_label' => 'DROP ' . str_pad((string) (($i % 18) + 1), 3, '0', STR_PAD_LEFT),
                'price' => $base,
                'sale_price' => $sale,
                'edition' => $editionRun,
                'rating' => round($r * 10) / 10,
                'reviews_count' => 12 + (int) round($this->rng($i + 3) * 240),
                'is_new' => $i % 6 === 1,
                'is_best' => $i % 5 === 2,
                'is_limited' => $i % 8 === 3,
                'is_active' => true,
                'stock' => $stock,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'sold_out_sizes' => $soldOut,
                'colors' => ['#000000', '#F5F5F5', '#2D2D2D'],
                'gallery' => ["img/designs/ax-{$num}.svg", "img/designs/ax-{$num}-b.svg", "img/designs/ax-{$num}.svg", "img/designs/ax-{$num}-b.svg"],
                'image' => "img/designs/ax-{$num}.svg",
                'image_hover' => "img/designs/ax-{$num}-b.svg",
                'blurb_short' => 'Heavyweight ' . rtrim(strtolower($n[1]), 's') . ' from the ' . $coll->name . ' line.',
                'description' => 'Cut from 320GSM brushed-back cotton with a boxy, dropped-shoulder fit. Screen-printed '
                    . $coll->name . ' graphics, tonal embroidery, and a numbered woven label. Garment-dyed for a'
                    . ' worn-in hand-feel. Produced once — ' . $editionRun . ' pieces, then retired.',
            ]);
        }

        // Drops (from the admin prototype)
        $byName = fn (string $name) => collect($collModels)->firstWhere('name', $name)?->id;
        $drops = [
            ['code' => 'DS-016', 'name' => 'Nichirin Season',   'collection' => 'Demon Slayer',  'status' => 'active',   'sold' => 187, 'total' => 300, 'revenue' => 32164, 'ends_at' => now()->addHours(14)],
            ['code' => 'DS-015', 'name' => 'Survey Recon',       'collection' => 'Attack Titan',  'status' => 'soldout',  'sold' => 300, 'total' => 300, 'revenue' => 47850, 'ends_at' => null],
            ['code' => 'DS-017', 'name' => 'Shadow Monarch II',  'collection' => 'Solo Hunter',   'status' => 'upcoming', 'sold' => 0,   'total' => 200, 'revenue' => 0,     'live_at' => now()->addDays(3)],
            ['code' => 'DS-014', 'name' => 'Grand Line Vol.3',   'collection' => 'Pirate King',   'status' => 'soldout',  'sold' => 400, 'total' => 400, 'revenue' => 59600, 'ends_at' => null],
            ['code' => 'DS-018', 'name' => 'Cursed Season III',  'collection' => 'Dark Curse',    'status' => 'upcoming', 'sold' => 0,   'total' => 250, 'revenue' => 0,     'live_at' => now()->addDays(7)],
            ['code' => 'DS-013', 'name' => 'Ki Surge Chapter 2', 'collection' => 'Saiyan Energy', 'status' => 'soldout',  'sold' => 350, 'total' => 350, 'revenue' => 51800, 'ends_at' => null],
        ];
        foreach ($drops as $d) {
            Drop::create([
                'code' => $d['code'],
                'name' => $d['name'],
                'collection_id' => $byName($d['collection']),
                'status' => $d['status'],
                'sold' => $d['sold'],
                'total' => $d['total'],
                'revenue' => $d['revenue'],
                'live_at' => $d['live_at'] ?? null,
                'ends_at' => $d['ends_at'] ?? null,
            ]);
        }
    }
}
