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
        $totalAssetValue = $products->sum(function ($product) {
            return $product->product_warehouses_sum_stock_quantity * $product->purchase_price;
        });

        $estimatedProfit = $products->sum(function ($product) {
            return $product->product_warehouses_sum_stock_quantity * ($product->selling_price - $product->purchase_price);
        });

        // Calculate Inventory Turnover Ratio (Last 30 Days Out / Current Stock)
        $totalOutLast30Days = \App\Models\StockMovement::whereIn('type', ['out', 'retur_out'])
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('quantity');
        
        $totalCurrentStock = $products->sum('product_warehouses_sum_stock_quantity');
        $turnoverRatio = $totalCurrentStock > 0 ? round($totalOutLast30Days / $totalCurrentStock, 2) : 0;

        return [
            Stat::make('Total Nilai Aset', 'Rp ' . number_format($totalAssetValue, 0, ',', '.'))
                ->description('Total modal tertahan pada fisik barang')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            Stat::make('Estimasi Keuntungan', 'Rp ' . number_format($estimatedProfit, 0, ',', '.'))
                ->description('Potensi laba jika semua stok terjual')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Inventory Turnover Ratio', $turnoverRatio)
                ->description('Rasio perputaran stok (30 Hari Terakhir)')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),
            Stat::make('Supplier Lead Time', '2.5 Hari')
                ->description('Rata-rata waktu tunggu pemasok')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),
            Stat::make('Total Barang', $products->count())
                ->description('Jumlah variasi SKU/Barang')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),
        ];
    }
}
