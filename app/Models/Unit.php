<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAuditLog;

class Unit extends Model
{
    use HasAuditLog;

    protected $fillable = ['name', 'symbol'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
