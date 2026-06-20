<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Catalog\Models\ProductCategory;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Product details')
                    ->columns()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('category_id')
                            ->label('Category')
                            ->options(fn (): array => ProductCategory::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('price')
                            ->numeric()
                            ->prefix('USD')
                            ->required()
                            ->minValue(0),

                        TextInput::make('stock_quantity')
                            ->label('Stock quantity')
                            ->numeric()
                            ->required()
                            ->minValue(0),

                        Textarea::make('description')
                            ->columnSpanFull(),

                    ]),
            ]);
    }
}
