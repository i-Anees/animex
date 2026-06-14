<?php

namespace App\Filament\Resources\Vouchers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->weight('bold')->searchable()->copyable(),
                TextColumn::make('type')->badge()->formatStateUsing(fn (string $state) => ucfirst($state)),
                TextColumn::make('value')->formatStateUsing(
                    fn ($state, $record) => $record->type === 'fixed' ? 'AED ' . (int) $state : (int) $state . '%'
                ),
                TextColumn::make('min_subtotal')->label('Min')->formatStateUsing(fn ($state) => 'AED ' . (int) $state),
                TextColumn::make('redeemed_count')->label('Used')->formatStateUsing(
                    fn ($state, $record) => $state . ' / ' . ($record->max_redemptions ?? '∞')
                ),
                TextColumn::make('expires_at')->date('M j, Y')->placeholder('No expiry')->sortable(),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
