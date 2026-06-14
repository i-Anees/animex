<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateDesigns extends Command
{
    protected $signature = 'designs:generate';

    protected $description = 'Generate original anime-streetwear graphic-tee SVG artwork for every product';

    /** Per-archetype emblem art, centred on (0,0), drawn with {INK} and {ACCENT}. */
    private function emblem(string $slug): string
    {
        return match ($slug) {
            'demon-slayer' => '
                <path d="M0,-185 C70,-80 60,55 0,185 C-60,55 -70,-80 0,-185 Z" fill="{ACCENT}" opacity="0.16"/>
                <g transform="rotate(26)"><rect x="-9" y="-175" width="18" height="300" rx="6" fill="{INK}"/><path d="M-9,-175 L9,-175 L0,-205 Z" fill="{INK}"/><rect x="-34" y="120" width="68" height="16" rx="4" fill="{ACCENT}"/><rect x="-11" y="134" width="22" height="60" rx="6" fill="{ACCENT}"/></g>
                <g transform="rotate(-26)"><rect x="-9" y="-175" width="18" height="300" rx="6" fill="{INK}"/><path d="M-9,-175 L9,-175 L0,-205 Z" fill="{INK}"/><rect x="-34" y="120" width="68" height="16" rx="4" fill="{ACCENT}"/><rect x="-11" y="134" width="22" height="60" rx="6" fill="{ACCENT}"/></g>
                <circle cx="0" cy="0" r="22" fill="{ACCENT}"/>',
            'attack-titan' => '
                <g fill="{INK}">
                  <path d="M-18,-110 C-120,-90 -180,-30 -195,70 C-120,30 -70,10 -22,0 Z"/>
                  <path d="M-18,-40 C-110,-15 -150,40 -160,110 C-95,75 -55,55 -20,45 Z"/>
                  <path d="M18,-110 C120,-90 180,-30 195,70 C120,30 70,10 22,0 Z" opacity="0.55"/>
                  <path d="M18,-40 C110,-15 150,40 160,110 C95,75 55,55 20,45 Z" opacity="0.55"/>
                </g>
                <path d="M-12,-150 L-12,150 L12,150 L12,-150 Z" fill="{ACCENT}"/>',
            'solo-hunter' => '
                <circle cx="0" cy="0" r="180" fill="none" stroke="{ACCENT}" stroke-width="5" opacity="0.4"/>
                <path d="M-140,70 L-140,-40 L-70,20 L0,-90 L70,20 L140,-40 L140,70 Z" fill="{INK}"/>
                <circle cx="-140" cy="-40" r="16" fill="{ACCENT}"/><circle cx="0" cy="-90" r="20" fill="{ACCENT}"/><circle cx="140" cy="-40" r="16" fill="{ACCENT}"/>
                <rect x="-140" y="78" width="280" height="22" fill="{INK}"/>',
            'ninja-legacy' => '
                <polygon points="0,-180 40,-40 180,0 40,40 0,180 -40,40 -180,0 -40,-40" fill="{INK}"/>
                <circle cx="0" cy="0" r="26" fill="{ACCENT}"/>
                <polygon points="0,-180 40,-40 180,0 40,40 0,180 -40,40 -180,0 -40,-40" fill="none" stroke="{ACCENT}" stroke-width="6" opacity="0.5"/>',
            'pirate-king' => '
                <g stroke="{INK}" stroke-width="34" stroke-linecap="round"><line x1="-150" y1="-150" x2="150" y2="150"/><line x1="150" y1="-150" x2="-150" y2="150"/></g>
                <circle cx="0" cy="-20" r="120" fill="{INK}"/>
                <rect x="-70" y="80" width="140" height="70" rx="14" fill="{INK}"/>
                <circle cx="-42" cy="-30" r="30" fill="{ACCENT}"/><circle cx="42" cy="-30" r="30" fill="{ACCENT}"/>
                <polygon points="0,5 -16,45 16,45" fill="{ACCENT}"/>',
            'dark-curse' => '
                <circle cx="0" cy="0" r="180" fill="none" stroke="{INK}" stroke-width="10"/>
                <polygon points="0,-170 50,40 -135,-80 135,-80 -50,40" fill="none" stroke="{ACCENT}" stroke-width="14" stroke-linejoin="round"/>
                <circle cx="0" cy="0" r="18" fill="{ACCENT}"/>',
            'ghoul-core' => '
                <path d="M-150,-150 H150 V90 Q150,170 0,200 Q-150,170 -150,90 Z" fill="{INK}"/>
                <line x1="0" y1="-150" x2="0" y2="200" stroke="{ACCENT}" stroke-width="12"/>
                <g stroke="{ACCENT}" stroke-width="9">
                  <line x1="-40" y1="-110" x2="40" y2="-110"/><line x1="-40" y1="-70" x2="40" y2="-70"/>
                </g>
                <polygon points="-110,-30 -30,-10 -110,30" fill="{ACCENT}"/>
                <polygon points="110,-30 30,-10 110,30" fill="{ACCENT}"/>',
            'saiyan-energy' => '
                <polygon points="0,-200 38,-70 165,-115 78,-15 200,30 65,40 110,170 0,80 -110,170 -65,40 -200,30 -78,-15 -165,-115 -38,-70" fill="{ACCENT}" opacity="0.25"/>
                <polygon points="0,-150 34,-48 150,-48 56,18 92,130 0,64 -92,130 -56,18 -150,-48 -34,-48" fill="{INK}"/>
                <circle cx="0" cy="6" r="24" fill="{ACCENT}"/>',
            default => '<circle cx="0" cy="0" r="120" fill="none" stroke="{ACCENT}" stroke-width="10"/>',
        };
    }

    private function buildSvg(Product $p, string $variant): string
    {
        $coll = $p->collection;
        $slug = $coll?->slug ?? 'default';
        $accent = $coll?->accent ?: '#FFFFFF';
        $name = strtoupper($coll?->name ?? 'ANIMEX');
        $tag = strtoupper($coll?->tag ?? 'ANIMEX WEAR');
        $drop = strtoupper($p->drop_label ?? '');
        $edition = $p->edition ? ('ED / ' . $p->edition) : '';

        if ($variant === 'b') {
            $bg1 = '#FFFFFF'; $bg2 = '#E6E6E6'; $garment = '#EDEDED'; $ink = '#141414';
        } else {
            $bg1 = '#262626'; $bg2 = '#0B0B0B'; $garment = $coll?->tone ?: '#161616'; $ink = '#F4F4F4';
        }

        $emblem = str_replace(['{INK}', '{ACCENT}'], [$ink, $accent], $this->emblem($slug));

        // T-shirt silhouette
        $tee = 'M430,205 C470,250 530,250 570,205 '
             . 'C620,212 660,235 700,255 '
             . 'C780,300 835,350 855,372 '
             . 'C835,408 770,452 735,438 '
             . 'L745,1075 L255,1075 L265,438 '
             . 'C230,452 165,408 145,372 '
             . 'C165,350 220,300 300,255 '
             . 'C340,235 380,212 430,205 Z';

        $fontStack = "Archivo, 'Arial Black', 'Helvetica Neue', sans-serif";
        $monoStack = "'Space Mono', 'Courier New', monospace";

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1250" width="1000" height="1250" role="img" aria-label="{$name} graphic tee">
  <defs>
    <radialGradient id="bg" cx="50%" cy="38%" r="75%">
      <stop offset="0%" stop-color="{$bg1}"/>
      <stop offset="100%" stop-color="{$bg2}"/>
    </radialGradient>
    <linearGradient id="shade" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#ffffff" stop-opacity="0.08"/>
      <stop offset="55%" stop-color="#000000" stop-opacity="0"/>
      <stop offset="100%" stop-color="#000000" stop-opacity="0.22"/>
    </linearGradient>
  </defs>
  <rect width="1000" height="1250" fill="url(#bg)"/>
  <path d="{$tee}" fill="{$garment}"/>
  <path d="{$tee}" fill="url(#shade)"/>
  <path d="M430,205 C470,250 530,250 570,205 C548,232 530,242 500,242 C470,242 452,232 430,205 Z" fill="#000000" opacity="0.18"/>
  <text x="500" y="312" text-anchor="middle" font-family="{$monoStack}" font-size="22" letter-spacing="8" fill="{$ink}" opacity="0.7">ANIMEX&#160;WEAR</text>
  <g transform="translate(500,540) scale(0.92)">{$emblem}</g>
  <text x="500" y="792" text-anchor="middle" font-family="{$fontStack}" font-weight="900" font-size="92" letter-spacing="-3" fill="{$ink}">{$name}</text>
  <text x="500" y="838" text-anchor="middle" font-family="{$monoStack}" font-size="24" letter-spacing="7" fill="{$accent}">{$tag}</text>
  <text x="265" y="1045" font-family="{$monoStack}" font-size="22" letter-spacing="4" fill="{$ink}" opacity="0.7">{$drop}</text>
  <text x="735" y="1045" text-anchor="end" font-family="{$monoStack}" font-size="22" letter-spacing="4" fill="{$accent}">{$edition}</text>
</svg>
SVG;
    }

    public function handle(): int
    {
        $dir = public_path('img/designs');
        File::ensureDirectoryExists($dir);

        $products = Product::with('collection')->get();
        $bar = $this->output->createProgressBar($products->count());

        foreach ($products as $p) {
            File::put("{$dir}/{$p->slug}.svg", $this->buildSvg($p, 'a'));
            File::put("{$dir}/{$p->slug}-b.svg", $this->buildSvg($p, 'b'));

            $front = "img/designs/{$p->slug}.svg";
            $back = "img/designs/{$p->slug}-b.svg";
            $p->update([
                'image' => $front,
                'image_hover' => $back,
                'gallery' => [$front, $back, $front, $back],
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Generated designs for {$products->count()} products into public/img/designs.");

        return self::SUCCESS;
    }
}
