<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersByStatusChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Orders by status';

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $statuses = ['unfulfilled', 'processing', 'fulfilled', 'cancelled'];
        $counts = Order::query()
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => array_map(fn ($s) => (int) ($counts[$s] ?? 0), $statuses),
                    'backgroundColor' => ['#9CA3AF', '#FBBF24', '#10B981', '#EF4444'],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['Unfulfilled', 'Processing', 'Fulfilled', 'Cancelled'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['position' => 'bottom']],
            'maintainAspectRatio' => false,
        ];
    }
}
