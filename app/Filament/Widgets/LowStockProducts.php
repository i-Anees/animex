<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockProducts extends TableWidget
{
    protected static ?int $sort = 5;

    protected static ?string $heading = 'Low stock — restock soon';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Product::query()->where('stock', '<=', 10)->orderBy('stock'))
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('title')->label('Product')->weight('bold')->searchable(),
                TextColumn::make('sku')->label('SKU')->searchable(),
                TextColumn::make('collection.name')->label('Series'),
                TextColumn::make('stock')->badge()->color(fn (int $state): string => $state < 1 ? 'danger' : ($state <= 5 ? 'warning' : 'gray')),
            ]);
    }
}
