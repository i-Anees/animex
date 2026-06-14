<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    // Single standard shipping method: AED 20, 2–3 business days.
    private const SHIPPING_FLAT = 20;
    private const TAX_RATE = 0.08;

    /** Persist a real order placed from the storefront SPA. */
    public function api(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:160'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'address' => ['required', 'string', 'max:200'],
            'city' => ['required', 'string', 'max:80'],
            'postcode' => ['nullable', 'string', 'max:20'],   // postcode is optional
            'country' => ['required', 'string', 'max:80'],
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
                if ($p->stock < $it['qty']) {
                    throw ValidationException::withMessages([
                        'items' => $p->title . ' only has ' . $p->stock . ' left in stock.',
                    ]);
                }
                $price = (float) ($p->sale_price ?? $p->price);
                $lineTotal = round($price * $it['qty'], 2);
                $subtotal += $lineTotal;
                $lines[] = [$p, $it, $price, $lineTotal];
            }

            // Apply voucher (re-validated server-side so a tampered/expired code can't slip through)
            $discount = 0;
            $voucherCode = null;
            if (! empty($data['coupon'])) {
                $voucher = Voucher::whereRaw('UPPER(code) = ?', [strtoupper(trim($data['coupon']))])
                    ->lockForUpdate()->first();
                if (! $voucher || $voucher->rejectionReason($subtotal)) {
                    throw ValidationException::withMessages([
                        'coupon' => $voucher ? $voucher->rejectionReason($subtotal) : 'Invalid voucher code.',
                    ]);
                }
                $discount = $voucher->discountFor($subtotal);
                $voucherCode = $voucher->code;
                $voucher->increment('redeemed_count');
            }

            $shipping = self::SHIPPING_FLAT;
            $tax = round(($subtotal - $discount) * self::TAX_RATE, 2);
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
                'coupon' => $voucherCode,
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
