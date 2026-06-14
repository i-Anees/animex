<?php

namespace App\Filament\Resources\Vouchers\Schemas;

use App\Models\Voucher;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn () => Voucher::generateCode())
                    ->helperText('Auto-generated. Use the refresh icon for a new code.')
                    ->suffixAction(
                        Action::make('regenerate')
                            ->icon('heroicon-m-arrow-path')
                            ->tooltip('Generate a new code')
                            ->action(fn ($set) => $set('code', Voucher::generateCode()))
                    ),
                Textarea::make('description')
                    ->rows(2)
                    ->placeholder('e.g. Welcome offer — 10% off your first drop'),
                Select::make('type')
                    ->options(['percent' => 'Percent (%)', 'fixed' => 'Fixed amount (AED)'])
                    ->default('percent')
                    ->required()
                    ->live(),
                TextInput::make('value')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->suffix(fn ($get) => $get('type') === 'fixed' ? 'AED' : '%'),
                TextInput::make('min_subtotal')
                    ->label('Minimum subtotal')
                    ->numeric()
                    ->default(0)
                    ->prefix('AED'),
                TextInput::make('max_redemptions')
                    ->label('Max redemptions')
                    ->numeric()
                    ->minValue(1)
                    ->helperText('Leave blank for unlimited.'),
                DateTimePicker::make('expires_at')
                    ->label('Expires at')
                    ->helperText('Leave blank for no expiry.'),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
