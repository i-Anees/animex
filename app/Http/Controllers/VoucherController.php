<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /** Validate a voucher code against the current cart subtotal. */
    public function apply(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:40'],
            'subtotal' => ['required', 'numeric', 'min:0'],
        ]);

        $voucher = Voucher::whereRaw('UPPER(code) = ?', [strtoupper(trim($data['code']))])->first();

        if (! $voucher) {
            return response()->json(['valid' => false, 'message' => 'Invalid voucher code.'], 422);
        }

        if ($reason = $voucher->rejectionReason((float) $data['subtotal'])) {
            return response()->json(['valid' => false, 'message' => $reason], 422);
        }

        return response()->json([
            'valid' => true,
            'code' => $voucher->code,
            'discount' => $voucher->discountFor((float) $data['subtotal']),
            'label' => $voucher->label(),
        ]);
    }
}
