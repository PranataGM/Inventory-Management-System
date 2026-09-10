<?php

namespace App\Filament\Widgets;

use App\Models\StockMovement;
use Filament\Widgets\ChartWidget;


class StockMovementChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Tren Mutasi Stok (Masuk vs Keluar)';

    protected function getData(): array
    {
        $months = collect(range(0, 5))->map(fn ($i) => now()->subMonths($i)->format('Y-m'))->reverse()->values();

        $dataMasuk = [];
        $dataKeluar = [];

        foreach ($months as $month) {
            $dataMasuk[] = StockMovement::where('type', 'in')->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month])->sum('quantity');
            $dataKeluar[] = StockMovement::where('type', 'out')->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month])->sum('quantity');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Barang Masuk',
                    'data' => $dataMasuk,
                    'borderColor' => '#10b981', // green
                    'fill' => false,
                ],
                [
                    'label' => 'Barang Keluar (Terjual)',
                    'data' => $dataKeluar,
                    'borderColor' => '#ef4444', // red
                    'fill' => false,
                ],
            ],
            'labels' => $months->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
