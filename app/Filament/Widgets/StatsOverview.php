<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $products = Product::withSum('productWarehouses', 'stock_quantity')->get();

        $totalAsset = 0;
        $potentialProfit = 0;

        foreach ($products as $product) {
            $stock = $product->product_warehouses_sum_stock_quantity ?? 0;
            $totalAsset += $product->purchase_price * $stock;
            $potentialProfit += ($product->selling_price - $product->purchase_price) * $stock;
        }

        return [
            Stat::make('Total Nilai Aset', 'Rp ' . number_format($totalAsset, 0, ',', '.'))
                ->description('Total modal stok saat ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            Stat::make('Estimasi Keuntungan', 'Rp ' . number_format($potentialProfit, 0, ',', '.'))
                ->description('Potensi untung jika semua terjual')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Barang', $products->count())
                ->description('Jumlah variasi SKU/Barang')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),
        ];
    }
}
