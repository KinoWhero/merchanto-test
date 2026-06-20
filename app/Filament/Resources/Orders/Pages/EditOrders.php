<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrdersResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
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
                                OrderStatus::Pending->value => 'Pending',
                                OrderStatus::Confirmed->value => 'Confirmed',
                                OrderStatus::Shipped->value => 'Shipped',
                                OrderStatus::Delivered->value => 'Delivered',
                            ]),

                        TextInput::make('total_amount')
                            ->label('Total amount')
                            ->numeric()
                            ->prefix('USD')
                            ->required()
                            ->minValue(0),
                    ]),
            ]);
    }
}
