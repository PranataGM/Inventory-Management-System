<?php
namespace App\Filament\Pages;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockMovement;
use Filament\Notifications\Notification;

class ScanQR extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-camera';
    protected static ?string $navigationLabel = 'Scanner QR';
    protected static string $view = 'filament.pages.scan-q-r';
    protected static ?string $title = 'Scanner QR Stok';
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }
    
    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('sku')->label('SKU Scanned')->required()->live()
                ->afterStateUpdated(function($state, callable $set) {
                     $p = Product::where('sku', $state)->first();
                     if($p) { 
                         $set('product_id', $p->id); 
                         $set('product_name', $p->name); 
                     } else {
                         $set('product_name', 'TIDAK DITEMUKAN');
                         $set('product_id', null);
                     }
                }),
            TextInput::make('product_name')->label('Nama Barang')->disabled(),
            TextInput::make('product_id')->hidden(),
            Select::make('warehouse_id')->label('Gudang')->options(Warehouse::pluck('name', 'id'))->required(),
            Select::make('type')->label('Mutasi')->options(['in' => 'Masuk', 'out' => 'Keluar'])->required(),
            TextInput::make('quantity')->label('Jumlah')->numeric()->minValue(1)->required(),
        ])->statePath('data');
    }
    
    public function submit()
    {
        $data = $this->form->getState();
        if(empty($data['product_id'])) {
            Notification::make()->title('Produk tidak ditemukan!')->danger()->send(); return;
        }
        
        // Cek stok keluar
        if ($data['type'] == 'out') {
            $stock = \App\Models\ProductWarehouse::where('product_id', $data['product_id'])->where('warehouse_id', $data['warehouse_id'])->value('stock_quantity') ?? 0;
            if ($stock < $data['quantity']) {
                Notification::make()->title('Stok tidak cukup!')->danger()->send(); return;
            }
        }

        StockMovement::create([
            'product_id' => $data['product_id'],
            'warehouse_id' => $data['warehouse_id'],
            'type' => $data['type'],
            'quantity' => $data['quantity'],
            'reason' => 'Dari QR Scanner',
        ]);
        Notification::make()->title('Stok berhasil diupdate!')->success()->send();
        $this->form->fill();
    }
}
