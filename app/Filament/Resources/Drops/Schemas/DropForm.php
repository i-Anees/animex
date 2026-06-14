<?php

namespace App\Filament\Resources\Drops\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DropForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('collection_id')
                    ->relationship('collection', 'name'),
                Select::make('status')
                    ->options(['upcoming' => 'Upcoming', 'active' => 'Active', 'soldout' => 'Soldout'])
                    ->default('upcoming')
                    ->required(),
                TextInput::make('sold')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('revenue')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                DateTimePicker::make('live_at'),
                DateTimePicker::make('ends_at'),
            ]);
    }
}
