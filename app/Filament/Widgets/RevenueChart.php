<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Revenue — last 30 days';

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $labels = [];
        $values = [];

        foreach (range(29, 0) as $d) {
            $day = Carbon::today()->subDays($d);
            $labels[] = $day->format('M j');
            $values[] = (float) Order::where('payment_status', 'paid')
                ->whereDate('placed_at', $day)
                ->sum('total');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue ($)',
                    'data' => $values,
                    'borderColor' => '#000000',
                    'backgroundColor' => 'rgba(0,0,0,0.06)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointRadius' => 0,
                    'pointHoverRadius' => 4,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
