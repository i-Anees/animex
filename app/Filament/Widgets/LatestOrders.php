<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestOrders extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Latest orders';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Order::query()->latest('placed_at'))
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('number')->label('Order')->weight('bold')->searchable(),
                TextColumn::make('customer_name')->label('Customer')->searchable(),
                TextColumn::make('total')->money('usd')->sortable(),
                TextColumn::make('payment_status')->badge()->color(fn (string $state): string => match ($state) {
                    'paid' => 'success', 'pending' => 'warning', 'refunded' => 'danger', default => 'gray',
                }),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'fulfilled' => 'success', 'processing' => 'warning', 'cancelled' => 'danger', default => 'gray',
                }),
                TextColumn::make('placed_at')->dateTime('M j, Y')->sortable(),
            ])
            ->recordActions([
                Action::make('receipt')
                    ->label('Receipt')
                    ->icon('heroicon-m-printer')
                    ->url(fn (Order $record): string => route('order.receipt', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
