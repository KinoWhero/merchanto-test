<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Order\Enums\OrderStatus;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('updated_at')->date()->searchable()->label('Date'),
                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->description(fn ($record) => $record->customer_email)
                    ->searchable(['customer_name', 'customer_email']),
                TextColumn::make('total_amount')->searchable()->label('Order Total')->money('USD'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (OrderStatus $state) => $state->color())
                    ->formatStateUsing(fn (OrderStatus $state) => $state->label()),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
