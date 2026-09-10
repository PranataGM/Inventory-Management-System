<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StockTake extends Model {
    protected $fillable = ['warehouse_id', 'user_id', 'date', 'status'];
    protected function casts(): array { return ['date' => 'date']; }
    
    protected static function booted()
    {
        static::creating(function ($stockTake) {
            if (auth()->check()) { $stockTake->user_id = auth()->id(); }
        });

        static::updated(function ($stockTake) {
            if ($stockTake->wasChanged('status') && $stockTake->status === 'completed') {
                foreach ($stockTake->items as $item) {
                    $item->difference = $item->actual_qty - $item->expected_qty;
                    $item->save();

                    if ($item->difference != 0) {
                        \App\Models\StockMovement::create([
                            'product_id' => $item->product_id,
                            'warehouse_id' => $stockTake->warehouse_id,
                            'type' => 'adjustment',
                            'quantity' => $item->difference,
                            'reason' => 'Opname penyesuaian selisih ' . $item->difference,
                        ]);
                    }
                }
            }
        });
    }

    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(StockTakeItem::class); }
}
