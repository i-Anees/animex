<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HeroSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('overline'),
                TextInput::make('title')
                    ->required(),
                Textarea::make('subtitle')
                    ->columnSpanFull(),
                FileUpload::make('image_id')
                    ->image(),
                FileUpload::make('image_url')
                    ->image(),
                TextInput::make('tone')
                    ->required()
                    ->default('#06121c'),
                TextInput::make('accent')
                    ->required()
                    ->default('#2BE2FF'),
                TextInput::make('accent2')
                    ->required()
                    ->default('#1E6BFF'),
                TextInput::make('cta_label')
                    ->required()
                    ->default('Shop the Drop'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
