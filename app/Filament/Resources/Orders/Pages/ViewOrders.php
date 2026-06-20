<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrdersResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Order\Enums\OrderStatus;

class ViewOrders extends ViewRecord
{
    protected static string $resource = OrdersResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Order')
                    ->schema([
                        TextEntry::make('id'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (OrderStatus $state) => $state->color())
                            ->formatStateUsing(fn (OrderStatus $state) => $state->label()),
                        TextEntry::make('total_amount')
                            ->money('USD'),
                    ]),

                Section::make('Customer')
                    ->schema([
                        TextEntry::make('customer_name'),
                        TextEntry::make('customer_email'),
                        TextEntry::make('customer_phone'),
                        TextEntry::make('customer_address'),
                    ]),

                Section::make('Order items')
                    ->columnSpanFull()
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('product_name')->label('Product'),
                                TextEntry::make('quantity')->numeric(),
                                TextEntry::make('unit_price')->money('USD'),
                                TextEntry::make('total_price')->money('USD'),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
