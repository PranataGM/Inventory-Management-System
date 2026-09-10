<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasAuditLog;

class Product extends Model {
    use SoftDeletes, HasAuditLog;
    protected $fillable = ['name', 'sku', 'category_id', 'purchase_price', 'selling_price', 'min_stock_threshold', 'qr_code'];
    public function category() { return $this->belongsTo(Category::class); }
    public function productWarehouses() { return $this->hasMany(ProductWarehouse::class); }
    public function stockMovements() { return $this->hasMany(StockMovement::class); }
    public function getTotalStockAttribute() { return $this->productWarehouses()->sum('stock_quantity'); }
}
