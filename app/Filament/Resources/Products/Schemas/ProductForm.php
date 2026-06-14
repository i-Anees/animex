<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Select::make('collection_id')
                    ->relationship('collection', 'name'),
                Select::make('category_id')
                    ->relationship('category', 'name'),
                TextInput::make('drop_label'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('sale_price')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('edition')
                    ->numeric(),
                TextInput::make('rating')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('reviews_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_new')
                    ->required(),
                Toggle::make('is_best')
                    ->required(),
                Toggle::make('is_limited')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('sizes'),
                TextInput::make('sold_out_sizes'),
                TextInput::make('colors'),
                TextInput::make('gallery'),
                FileUpload::make('image')
                    ->image(),
                FileUpload::make('image_hover')
                    ->image(),
                TextInput::make('blurb_short'),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}
