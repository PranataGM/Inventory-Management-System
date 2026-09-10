<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Product;

class DeadStockProducts extends BaseWidget
{
    protected static ?int $sort = 5;
    protected static ?string $heading = 'Analisis Dead Stock (Tidak Ada Keluar 3 Bulan Terakhir)';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->whereDoesntHave('stockMovements', function($q) {
                        $q->where('type', 'out')->where('created_at', '>=', now()->subMonths(3));
                    })
                    ->whereHas('productWarehouses', function($q) {
                        $q->where('stock_quantity', '>', 0);
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Barang')->searchable(),
                Tables\Columns\TextColumn::make('sku')->label('SKU'),
                Tables\Columns\TextColumn::make('total_stock')->label('Sisa Stok Mengendap'),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->label('Nilai Modal')
                    ->money('IDR')
                    ->state(function (Product $record): float {
                        return $record->purchase_price * $record->total_stock;
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->color('danger')
                    ->state('Dead Stock'),
            ])
            ->defaultPaginationPageOption(5);
    }
}
