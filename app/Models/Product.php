<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'name', 'production_price', 'selling_price', 'created_at', 'updated_at'
    ];

    public function salesOrderItems(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class, 'product_id');
    }
}
