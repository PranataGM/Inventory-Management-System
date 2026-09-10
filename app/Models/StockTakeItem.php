<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StockTakeItem extends Model {
    protected $fillable = ['stock_take_id', 'product_id', 'expected_qty', 'actual_qty', 'difference', 'reason'];
    public function stockTake() { return $this->belongsTo(StockTake::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
