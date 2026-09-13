<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\StockMovement;
use App\Models\ProductWarehouse;
use App\Filament\Imports\ProductImporter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->importer(ProductImporter::class)
                ->label('Import Barang (CSV/Excel)'),
            Actions\Action::make('transfer_stock')
                ->label('Transfer Stok')
                ->icon('heroicon-o-arrows-right-left')
                ->color('warning')
                ->visible(fn () => auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Staff Gudang'))
                ->form([
                    Select::make('product_id')
                        ->label('Pilih Barang')
                        ->relationship('productWarehouses.product', 'name')
                        ->options(\App\Models\Product::pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                    Select::make('from_warehouse_id')
                        ->label('Dari Gudang')
                        ->options(\App\Models\Warehouse::pluck('name', 'id'))
                        ->required(),
                    Select::make('to_warehouse_id')
                        ->label('Ke Gudang')
                        ->options(\App\Models\Warehouse::pluck('name', 'id'))
                        ->required()
                        ->different('from_warehouse_id'),
                    TextInput::make('quantity')
                        ->label('Jumlah')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->rule(static function (\Filament\Forms\Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $currentStock = \App\Models\ProductWarehouse::where('product_id', $get('product_id'))
                                    ->where('warehouse_id', $get('from_warehouse_id'))
                                    ->value('stock_quantity') ?? 0;
                                if ($value > $currentStock) {
                                    $fail("Stok di gudang asal tidak mencukupi! Sisa: {$currentStock}.");
                                }
                            };
                        }),
                ])
                ->action(function (array $data) {
                    DB::transaction(function () use ($data) {
                        $source = ProductWarehouse::firstOrCreate(
                            ['product_id' => $data['product_id'], 'warehouse_id' => $data['from_warehouse_id']],
                            ['stock_quantity' => 0]
                        );

                        if ($source->stock_quantity < $data['quantity']) {
                            Notification::make()
                                ->title('Stok Tidak Cukup!')
                                ->danger()
                                ->send();
                            return;
                        }

                        StockMovement::create([
                            'product_id' => $data['product_id'],
                            'warehouse_id' => $data['from_warehouse_id'],
                            'type' => 'out',
                            'quantity' => $data['quantity'],
                            'reason' => 'Transfer keluar ke Gudang ' . \App\Models\Warehouse::find($data['to_warehouse_id'])->name,
                        ]);

                        StockMovement::create([
                            'product_id' => $data['product_id'],
                            'warehouse_id' => $data['to_warehouse_id'],
                            'type' => 'in',
                            'quantity' => $data['quantity'],
                            'reason' => 'Transfer masuk dari Gudang ' . \App\Models\Warehouse::find($data['from_warehouse_id'])->name,
                        ]);

                        Notification::make()
                            ->title('Transfer Berhasil')
                            ->success()
                            ->send();
                    });
                }),
            Actions\CreateAction::make(),
        ];
    }
}
