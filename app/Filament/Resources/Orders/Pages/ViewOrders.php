<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrdersResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Order\Enums\OrderStatus;

class ViewOrders extends ViewRecord
{
    protected static string $resource = OrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Order')
                    ->schema([
                        TextEntry::make('id'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (OrderStatus $state): string => match ($state) {
                                OrderStatus::Pending => 'gray',
                                OrderStatus::Confirmed => 'warning',
                                OrderStatus::Shipped, OrderStatus::Delivered => 'success',
                            }),
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
            ]);
    }
}
