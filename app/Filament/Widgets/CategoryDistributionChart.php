<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CategoryDistributionChart extends ChartWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;

    protected static ?string $heading = 'Distribusi Barang per Kategori';

    protected function getData(): array
    {
        $categories = Category::withCount('products')->get();
        
        $labels = [];
        $data = [];
        $backgroundColors = [];

        $colors = ['#f87171', '#fb923c', '#fbbf24', '#a3e635', '#34d399', '#2dd4bf', '#38bdf8', '#818cf8', '#c084fc', '#f472b6'];

        foreach ($categories as $index => $category) {
            if ($category->products_count > 0) {
                $labels[] = $category->name;
                $data[] = $category->products_count;
                $backgroundColors[] = $colors[$index % count($colors)];
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Barang',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
