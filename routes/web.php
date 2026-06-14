<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

// Storefront single-page app (original ANIMEX prototype, served verbatim with live data)
Route::get('/', [StorefrontController::class, 'home'])->name('home');

// Real order placement from the SPA checkout
Route::post('/api/orders', [CheckoutController::class, 'api'])->name('orders.place');

// Validate a discount voucher against the cart subtotal
Route::post('/api/voucher', [VoucherController::class, 'apply'])->name('voucher.apply');

// Order receipt — view, edit details, print (admin only)
Route::middleware('auth')->group(function () {
    Route::get('/orders/{order}/receipt', [ReceiptController::class, 'show'])->name('order.receipt');
    Route::put('/orders/{order}/receipt', [ReceiptController::class, 'update'])->name('order.receipt.update');
});