<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Drop;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CommerceSeeder extends Seeder
{
    private function rng(float $seed): float
    {
        $x = sin($seed + 17) * 10000;
        return $x - floor($x);
    }

    public function run(): void
    {
        $first = ['Kenji', 'Yuki', 'Sora', 'Ren', 'Hiro', 'Mika', 'Aya', 'Taro', 'Emi', 'Kai', 'Zara', 'Noa', 'Lena', 'Marcus', 'Aiden', 'Sophie'];
        $last = ['Nakamura', 'Tanaka', 'Watanabe', 'Chen', 'Park', 'Kim', 'Santos', 'Wilson', 'Okafor', 'Müller', 'Patel', 'Russo', 'Ali', 'Hassan'];
        $cities = ['Tokyo', 'Seoul', 'London', 'New York', 'Los Angeles', 'Sydney', 'Berlin', 'Toronto', 'Paris', 'Amsterdam'];

        $customers = [];
        $usedEmails = [];
        for ($i = 0; $i < 32; $i++) {
            $fn = $first[$i % count($first)];
            $ln = $last[($i + 7) % count($last)];
            $emailBase = strtolower($fn) . '.' . preg_replace('/[^a-z]/', '', strtolower($ln));
            $email = $emailBase . '@email.com';
            if (in_array($email, $usedEmails, true)) {
                $email = $emailBase . ($i + 1) . '@email.com';
            }
            $usedEmails[] = $email;

            $customers[$i] = Customer::create([
                'name' => "$fn $ln",
                'email' => $email,
                'city' => $cities[$i % count($cities)],
                'status' => $this->rng($i + 2) > 0.15 ? 'active' : 'inactive',
                'joined_at' => now()->subDays((int) floor($this->rng($i + 8) * 365)),
            ]);
        }

        $productNames = [
            'Shadow Clan Hoodie', 'Breathing Form Tee', 'Survey Corps Bomber', 'Monarch Heavyweight',
            'Cursed Seal Tee', 'Grand Line Cargo', 'Hidden Leaf Crew', 'Beyond Limits Tee',
            'Ward 18 Trench', 'Ki Surge Hoodie', 'Blade Division Cap', 'Domain Zip Hoodie',
        ];
        $productsByTitle = Product::whereIn('title', $productNames)->get()->keyBy('title');
        $dropCodes = Drop::pluck('id', 'code'); // code => id
        $dropOrder = ['DS-016', 'DS-015', 'DS-017', 'DS-014', 'DS-018', 'DS-013'];

        $orderStatuses = ['fulfilled', 'fulfilled', 'fulfilled', 'unfulfilled', 'processing', 'cancelled'];
        $paymentStatuses = ['paid', 'paid', 'paid', 'paid', 'pending', 'refunded'];

        for ($i = 0; $i < 48; $i++) {
            $c = $customers[$i % count($customers)];
            $itemCount = 1 + (int) floor($this->rng($i + 1) * 3);
            $daysAgo = (int) floor($this->rng($i + 11) * 60);
            $placedAt = now()->subDays($daysAgo);

            $lineData = [];
            $subtotal = 0;
            for ($j = 0; $j < $itemCount; $j++) {
                $pname = $productNames[($i + $j) % count($productNames)];
                $price = 78 + (int) floor($this->rng($i * 3 + $j + 2) * 10) * 16;
                $product = $productsByTitle->get($pname);
                $subtotal += $price;
                $lineData[] = [
                    'product_id' => $product?->id,
                    'name' => $pname,
                    'sku' => $product?->sku,
                    'size' => ['S', 'M', 'L', 'XL', 'XXL'][$j % 5],
                    'color' => '#000000',
                    'qty' => 1,
                    'price' => $price,
                    'line_total' => $price,
                ];
            }

            $tax = round($subtotal * 0.08, 2);
            $shipping = $subtotal >= 150 ? 0 : 12;
            $total = $subtotal + $tax + $shipping;

            $dropCode = $i % 3 === 0 ? $dropOrder[$i % count($dropOrder)] : null;

            $order = Order::create([
                'number' => 'AX-' . str_pad((string) (10840 + $i), 5, '0', STR_PAD_LEFT),
                'customer_id' => $c->id,
                'email' => $c->email,
                'customer_name' => $c->name,
                'status' => $orderStatuses[$i % count($orderStatuses)],
                'payment_status' => $paymentStatuses[$i % count($paymentStatuses)],
                'subtotal' => $subtotal,
                'discount' => 0,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'city' => $c->city,
                'drop_id' => $dropCode ? $dropCodes->get($dropCode) : null,
                'placed_at' => $placedAt,
                'created_at' => $placedAt,
                'updated_at' => $placedAt,
            ]);

            foreach ($lineData as $line) {
                $order->items()->create($line);
            }
        }
    }
}
