<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class FastMovingChart extends ChartWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;

    protected static ?string $heading = 'Top 5 Barang Fast-Moving (30 Hari Terakhir)';

    protected function getData(): array
    {
        $products = Product::query()
            ->withSum(['stockMovements' => function($q) {
                $q->where('type', 'out')->where('created_at', '>=', now()->subDays(30));
            }], 'quantity')
            ->having('stock_movements_sum_quantity', '>', 0)
            ->orderBy('stock_movements_sum_quantity', 'desc')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Terjual/Keluar',
                    'data' => $products->pluck('stock_movements_sum_quantity')->toArray(),
                    'backgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => $products->pluck('name')->map(fn($n) => str($n)->limit(15))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
