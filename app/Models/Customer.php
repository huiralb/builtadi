<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $table = 'customers';
    protected $fillable = [
        'name', 'address', 'phone', 'created_at', 'updated_at'
    ];

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class, 'customer_id');
    }
}
