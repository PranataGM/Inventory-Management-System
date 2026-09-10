<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockTakeResource\Pages;
use App\Models\StockTake;
use App\Models\ProductWarehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockTakeResource extends Resource
{
    protected static ?string $model = StockTake::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Opname Stok';
    protected static ?string $pluralModelLabel = 'Opname Stok';

    public static function canCreate(): bool { return auth()->user()->hasRole('Admin'); }
    public static function canEdit($record): bool { return auth()->user()->hasRole('Admin') && $record->status === 'pending'; }
    public static function canDelete($record): bool { return auth()->user()->hasRole('Admin') && $record->status === 'pending'; }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('warehouse_id')
                    ->label('Gudang')
                    ->relationship('warehouse', 'name')
                    ->required()
                    ->disabled(fn (?StockTake $record) => $record !== null), // disable edit on warehouse
                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal Opname')
                    ->required()
                    ->default(now()),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending (Draft)',
                        'completed' => 'Selesai (Terapkan Penyesuaian)',
                    ])
                    ->default('pending')
                    ->required(),
                
                Forms\Components\Repeater::make('items')
                    ->relationship('items')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Barang')
                            ->options(\App\Models\Product::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => 
                                $set('expected_qty', ProductWarehouse::where('product_id', $state)->where('warehouse_id', $get('../../warehouse_id'))->value('stock_quantity') ?? 0)
                            ),
                        Forms\Components\TextInput::make('expected_qty')
                            ->label('Stok Tercatat')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('actual_qty')
                            ->label('Stok Fisik (Aktual)')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('reason')
                            ->label('Keterangan Selisih')
                            ->maxLength(255),
                    ])
                    ->columns(4)
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->label('Tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('warehouse.name')->label('Gudang')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Pencatat')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Draft',
                        'completed' => 'Selesai',
                        default => $state,
                    }),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockTakes::route('/'),
            'create' => Pages\CreateStockTake::route('/create'),
            'edit' => Pages\EditStockTake::route('/{record}/edit'),
        ];
    }
}
