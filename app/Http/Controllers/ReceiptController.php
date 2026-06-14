<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function show(Order $order)
    {
        $order->load('items', 'customer');

        return view('receipt', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'address' => ['nullable', 'string', 'max:200'],
            'city' => ['nullable', 'string', 'max:80'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:80'],
            'status' => ['required', 'in:unfulfilled,processing,fulfilled,cancelled'],
            'payment_status' => ['required', 'in:pending,paid,refunded'],
        ]);

        $order->update([
            'customer_name' => $data['customer_name'],
            'email' => $data['email'],
            'city' => $data['city'],
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
            'shipping_address' => [
                'address' => $data['address'],
                'city' => $data['city'],
                'postcode' => $data['postcode'],
                'country' => $data['country'],
            ],
        ]);

        return redirect()
            ->route('order.receipt', $order)
            ->with('saved', 'Receipt details updated.');
    }
}
