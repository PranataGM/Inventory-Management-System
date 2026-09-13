<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAuditLog;

class Supplier extends Model
{
    use HasAuditLog;

    protected $fillable = ['name', 'contact_person', 'phone', 'address'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
