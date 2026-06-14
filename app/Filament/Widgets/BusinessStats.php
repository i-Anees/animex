<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class BusinessStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $prevStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $prevEnd = $monthStart->copy();

        $paid = Order::where('payment_status', 'paid');

        $revenue = (float) (clone $paid)->sum('total');
        $revThis = (float) (clone $paid)->where('placed_at', '>=', $monthStart)->sum('total');
        $revPrev = (float) (clone $paid)->whereBetween('placed_at', [$prevStart, $prevEnd])->sum('total');
        $revDelta = $revPrev > 0 ? round((($revThis - $revPrev) / $revPrev) * 100) : 0;

        $orders = Order::count();
        $ordersThis = Order::where('placed_at', '>=', $monthStart)->count();

        $customers = Customer::count();

        $paidCount = (clone $paid)->count();
        $aov = $paidCount > 0 ? $revenue / $paidCount : 0;

        // 14-day spark series for revenue
        $spark = collect(range(13, 0))->map(function ($d) {
            $day = Carbon::today()->subDays($d);
            return (float) Order::where('payment_status', 'paid')
                ->whereDate('placed_at', $day)->sum('total');
        })->all();

        return [
            Stat::make('Total Revenue', '$' . number_format($revenue, 0))
                ->description(($revDelta >= 0 ? '+' : '') . $revDelta . '% vs last month')
                ->descriptionIcon($revDelta >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revDelta >= 0 ? 'success' : 'danger')
                ->chart($spark),

            Stat::make('Total Orders', number_format($orders))
                ->description($ordersThis . ' this month')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),

            Stat::make('Customers', number_format($customers))
                ->description('Lifetime registered')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),

            Stat::make('Avg Order Value', '$' . number_format($aov, 0))
                ->description('Across paid orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
