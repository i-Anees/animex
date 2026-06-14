<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private const SHIP_COSTS = ['std' => 0, 'exp' => 12, 'next' => 22];

    /** Persist a real order placed from the storefront SPA. */
    public function api(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:160'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'address' => ['required', 'string', 'max:200'],
            'city' => ['required', 'string', 'max:80'],
            'postcode' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:80'],
            'ship' => ['nullable', 'string', 'in:std,exp,next'],
            'coupon' => ['nullable', 'string', 'max:40'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:products,id'],
            'items.*.size' => ['nullable', 'string', 'max:8'],
            'items.*.color' => ['nullable', 'string', 'max:40'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $order = DB::transaction(function () use ($data) {
            $products = Product::whereIn('id', collect($data['items'])->pluck('id'))
                ->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            $lines = [];
            foreach ($data['items'] as $it) {
                $p = $products[$it['id']];
                $price = (float) ($p->sale_price ?? $p->price);
                $lineTotal = round($price * $it['qty'], 2);
                $subtotal += $lineTotal;
                $lines[] = [$p, $it, $price, $lineTotal];
            }

            $discount = ! empty($data['coupon']) ? round($subtotal * 0.10, 2) : 0;
            $shipping = $subtotal > 200 ? 0 : (self::SHIP_COSTS[$data['ship'] ?? 'exp'] ?? 12);
            $tax = round(($subtotal - $discount) * 0.08, 2);
            $total = round($subtotal - $discount + $shipping + $tax, 2);
            $name = trim($data['first_name'] . ' ' . $data['last_name']);

            $customer = Customer::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $name, 'city' => $data['city'], 'status' => 'active', 'joined_at' => now()]
            );

            $order = Order::create([
                'number' => Order::generateNumber(),
                'customer_id' => $customer->id,
                'email' => $data['email'],
                'customer_name' => $name,
                'status' => 'unfulfilled',
                'payment_status' => 'paid',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'coupon' => $data['coupon'] ?? null,
                'city' => $data['city'],
                'shipping_address' => [
                    'address' => $data['address'],
                    'city' => $data['city'],
                    'postcode' => $data['postcode'],
                    'country' => $data['country'],
                ],
                'placed_at' => now(),
            ]);

            foreach ($lines as [$p, $it, $price, $lineTotal]) {
                $order->items()->create([
                    'product_id' => $p->id,
                    'name' => $p->title,
                    'sku' => $p->sku,
                    'size' => $it['size'] ?? null,
                    'color' => $it['color'] ?? null,
                    'qty' => $it['qty'],
                    'price' => $price,
                    'line_total' => $lineTotal,
                ]);
                $p->decrement('stock', min($it['qty'], $p->stock));
            }

            return $order;
        });

        return response()->json([
            'number' => $order->number,
            'total' => (float) $order->total,
        ]);
    }
}
