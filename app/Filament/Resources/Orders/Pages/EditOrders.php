<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrdersResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Modules\Order\Enums\OrderStatus;

class EditOrders extends EditRecord
{
    protected static string $resource = OrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Customer')
                    ->columns()
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('customer_email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('customer_phone')
                            ->label('Phone')
                            ->maxLength(255),

                        Textarea::make('customer_address')
                            ->label('Address')
                            ->columnSpanFull(),
                    ]),

                Section::make('Order')
                    ->columns()
                    ->schema([
                        Select::make('status')
                            ->required()
                            ->options([
                                OrderStatus::Pending->value => OrderStatus::Pending->label(),
                                OrderStatus::Confirmed->value => OrderStatus::Confirmed->label(),
                                OrderStatus::Shipped->value => OrderStatus::Shipped->label(),
                                OrderStatus::Delivered->value => OrderStatus::Delivered->label(),
                            ]),

                        TextInput::make('total_amount')
                            ->label('Total amount')
                            ->numeric()
                            ->prefix('USD')
                            ->required()
                            ->minValue(0),
                    ]),

                Section::make('Order items')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->label('')
                            ->schema([
                                TextInput::make('product_name')
                                    ->label('Product')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('unit_price')
                                    ->label('Unit price')
                                    ->numeric()
                                    ->prefix('USD')
                                    ->required()
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set): void {
                                        $set('total_price', (float) $get('unit_price') * (int) $get('quantity'));
                                    }),

                                // TODO: Add stock quantity
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set): void {
                                        $set('total_price', (float) $get('unit_price') * (int) $get('quantity'));
                                    }),

                                TextInput::make('total_price')
                                    ->label('Total')
                                    ->numeric()
                                    ->prefix('USD')
                                    ->readOnly()
                                    ->dehydrated(),
                            ])
                            ->columns(4)
                            ->deletable()
                            ->reorderable(false)
                            ->addable(false),
                    ]),
            ]);
    }
}
