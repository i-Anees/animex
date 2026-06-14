<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            ['code' => 'ANIMEX10', 'description' => 'Welcome offer — 10% off', 'type' => 'percent', 'value' => 10, 'min_subtotal' => 0, 'is_active' => true],
            ['code' => 'DROP50',   'description' => 'AED 50 off orders over AED 300', 'type' => 'fixed', 'value' => 50, 'min_subtotal' => 300, 'is_active' => true],
        ];

        foreach ($vouchers as $v) {
            Voucher::updateOrCreate(['code' => $v['code']], $v);
        }
    }
}
