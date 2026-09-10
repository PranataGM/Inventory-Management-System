<?php

namespace App\Filament\Exports;

use App\Models\StockMovement;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StockMovementExporter extends Exporter
{
    protected static ?string $model = StockMovement::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('created_at')->label('Tanggal Mutasi'),
            ExportColumn::make('product.name')->label('Nama Barang'),
            ExportColumn::make('warehouse.name')->label('Gudang'),
            ExportColumn::make('type')->label('Tipe Mutasi'),
            ExportColumn::make('quantity')->label('Jumlah'),
            ExportColumn::make('reason')->label('Keterangan'),
            ExportColumn::make('user.name')->label('User Pencatat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your stock movement export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
