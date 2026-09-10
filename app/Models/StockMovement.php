<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasAuditLog;

class StockMovement extends Model {
    use SoftDeletes, HasAuditLog;
    protected $fillable = ['product_id', 'warehouse_id', 'user_id', 'type', 'quantity', 'reason'];
    
    protected static function booted()
    {
        static::creating(function ($movement) {
            if (auth()->check()) {
                $movement->user_id = auth()->id();
            }
        });

        static::created(function ($movement) {
            $productWarehouse = \App\Models\ProductWarehouse::firstOrCreate(
                ['product_id' => $movement->product_id, 'warehouse_id' => $movement->warehouse_id],
                ['stock_quantity' => 0]
            );

            if ($movement->type === 'in') {
                $productWarehouse->increment('stock_quantity', $movement->quantity);
            } elseif ($movement->type === 'out') {
                $productWarehouse->decrement('stock_quantity', $movement->quantity);
            } elseif ($movement->type === 'adjustment') {
                $productWarehouse->stock_quantity += $movement->quantity;
                $productWarehouse->save();
            }
        });
    }

    public function product() { return $this->belongsTo(Product::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function user() { return $this->belongsTo(User::class); }
}
