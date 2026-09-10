<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Warehouse extends Model {
    protected $fillable = ['name', 'location'];
    public function productWarehouses() { return $this->hasMany(ProductWarehouse::class); }
}
